<?php 
namespace App\Services;

use App\Repositories\SeasonRepository;
use App\Models\Season;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Services\TeamSelection\ITeamSelector;
use App\Services\Fixture\IFixtureGenerator;
use App\Services\Simulation\IMatchSimulator;
use App\Services\Prediction\IPredictor;

class SeasonService
{
    protected SeasonRepository $seasons;

    public function __construct(
        SeasonRepository $seasons, 
        ITeamSelector $teamSelector, 
        IFixtureGenerator $fixtureGenerator, 
        IMatchSimulator $matchSimulator,
        IPredictor $predictor
    ) {
        $this->seasons = $seasons;
        $this->teamSelector = $teamSelector;
        $this->fixtureGenerator = $fixtureGenerator;
        $this->matchSimulator = $matchSimulator;
        $this->predictor = $predictor;
    }
    
    public function getShowPage(string $slug)
    {
        $season = $this->seasons->getSeasonWithSlug($slug);
        
        $props = [
            'season' => $season
        ];

        // Add predictions if half of the season has ended
        if ($season->current_week > ($season->total_weeks / 2) && $season->total_weeks != 0) {
            $props['predictions'] = $this->predictor->predict($season);
        }

        return Inertia::render('Seasons/Show', $props);
    }

    public function getCreatePage()
    {
        $season = $this->seasons->getActiveSeason();
        $props = [];
        if ($season) {
            $props['season_slug'] = $season->slug;
        }  
        return Inertia::render('Seasons/Create', $props);
    }

    public function create(string $name): Season
    {
        if ($this->seasons->hasActiveSeason()) {
            throw ValidationException::withMessages([
                'season' => 'An active season already exists.'
            ]);
        }

        return $this->seasons->create([
            'name'         => $name,
            'slug'         => Str::slug($name)
        ]);
    }

    public function selectTeams(int $teamCount)
    {
        $teams = $this->teamSelector->select($teamCount);
        return response()->json($teams);
    }

    public function start(string $slug, array $teams)
    {
        try {
            return \DB::transaction(function () use ($slug, $teams) {
                $season = $this->seasons->getSeasonWithSlug($slug);

                // Add teams to season
                $this->seasons->addTeams($season, $teams);
                $season->load('teams');

                // Update season weeks based on team count
                $this->seasons->updateSeasonWeeks($season, count($teams));

                // Generate and add fixtures
                $fixtures = $this->fixtureGenerator->generate($season->teams);
                \Log::info('Generated fixtures count: ' . count($fixtures));
                \Log::info('Generated fixtures: ' . json_encode($fixtures));
                
                $this->seasons->addFixtures($season, $fixtures);
                $season->load('fixtures');
                
                \Log::info('Season fixtures count after adding: ' . $season->fixtures()->count());

                // Create initial standings
                $standingsData = $season->teams->map(function ($team) use ($season) {
                    return [
                        'season_id' => $season->id,
                        'team_id' => $team->id
                    ];
                })->toArray();
                $this->seasons->addStandings($season, $standingsData);
                $season->load(['standings' => function($query) {
                    $query->with('seasonTeam.team')
                          ->orderBy('points', 'desc')
                          ->orderBy('goal_difference', 'desc');
                }]);

                return response()->json($season);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to start season: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'season' => 'Failed to start season. Please try again.'
            ]);
        }
    }

    public function simulateWeek(string $slug)
    {
        try {
            return \DB::transaction(function () use ($slug) {
                $season = $this->seasons->getSeasonWithSlug($slug);
                if ($season->is_active == false) {
                    throw ValidationException::withMessages([
                        'season' => 'Season is not active.'
                    ]);
                }
                $matches = $season->thisWeekMatches;
                
                foreach ($matches as $match) {
                    $result = $this->matchSimulator->simulate($match);
                    $match->home_team_score = $result['home_team_score'];
                    $match->away_team_score = $result['away_team_score'];
                    $match->played = true;
                    $match->save();

                    $this->updateStandings($season, $match, $result);
                }
                
                $season->current_week++;
                $season->save();
                $this->seasons->makePassiveIfEnded($season);
                return response()->json([
                    'matches' => $matches->load(['homeTeam', 'awayTeam'])
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to simulate week: ' . $e->getMessage());
            dd($e->getMessage());
            throw ValidationException::withMessages([
                'simulation' => 'Failed to simulate matches. Please try again.'
            ]);
        }
    }

    public function simulateAll(string $slug)
    {
        try {
            return \DB::transaction(function () use ($slug) {
                $season = $this->seasons->getSeasonWithSlug($slug);
                $allMatches = [];
                
                // Simulate all remaining weeks
                while ($season->current_week <= $season->total_weeks) {
                    $matches = $season->thisWeekMatches;
                    
                    foreach ($matches as $match) {
                        $result = $this->matchSimulator->simulate($match);
                        $match->home_team_score = $result['home_team_score'];
                        $match->away_team_score = $result['away_team_score'];
                        $match->played = true;
                        $match->save();

                        $this->updateStandings($season, $match, $result);
                        $allMatches[] = $match;
                    }
                    
                    $season->current_week++;
                    $season->save();
                    
                    // Refresh the season model to get updated current_week
                    $season = $season->fresh();
                }

                $this->seasons->makePassiveIfEnded($season);

                return response()->json([
                    'matches' => $allMatches,
                    'season' => $season->load('standings.seasonTeam.team')
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to simulate season: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'simulation' => 'Failed to simulate season. Please try again.'
            ]);
        }
    }

    private function updateStandings($season, $match, $result) {
        $homeTeamStanding = $season->standings->where('team_id', $match->home_team_id)->first();
        $awayTeamStanding = $season->standings->where('team_id', $match->away_team_id)->first();
        
        $homeScore = $result['home_team_score'];
        $awayScore = $result['away_team_score'];
        $goalDiff = $homeScore - $awayScore;

        // Update basic stats for both teams
        $this->updateTeamStats($homeTeamStanding, $homeScore, $awayScore);
        $this->updateTeamStats($awayTeamStanding, $awayScore, $homeScore);

        // Update points and specific match results
        if ($homeScore > $awayScore) {
            $homeTeamStanding->update([
                'won' => $homeTeamStanding->won + 1,
                'points' => $homeTeamStanding->points + 3
            ]);
            $awayTeamStanding->update([
                'lost' => $awayTeamStanding->lost + 1
            ]);
        } elseif ($homeScore < $awayScore) {
            $homeTeamStanding->update([
                'lost' => $homeTeamStanding->lost + 1
            ]);
            $awayTeamStanding->update([
                'won' => $awayTeamStanding->won + 1,
                'points' => $awayTeamStanding->points + 3
            ]);
        } else {
            $homeTeamStanding->update([
                'drawn' => $homeTeamStanding->drawn + 1,
                'points' => $homeTeamStanding->points + 1
            ]);
            $awayTeamStanding->update([
                'drawn' => $awayTeamStanding->drawn + 1,
                'points' => $awayTeamStanding->points + 1
            ]);
        }
    }

    private function updateTeamStats($standing, $goalsFor, $goalsAgainst) {
        $standing->update([
            'played' => $standing->played + 1,
            'goals_for' => $standing->goals_for + $goalsFor,
            'goals_against' => $standing->goals_against + $goalsAgainst
        ]);
    }

    public function reset(string $slug)
    {
        try {
            return \DB::transaction(function () use ($slug) {
                $season = $this->seasons->getSeasonWithSlug($slug);
                
                // Delete all related data
                $season->standings()->delete();
                $season->fixtures()->delete();
                $season->teams()->delete();
                
                // Reset season properties
                $season->update([
                    'current_week' => 1,
                    'total_weeks' => 0,
                    'is_active' => true
                ]);

                return response()->json($season);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to reset season: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'season' => 'Failed to reset season. Please try again.'
            ]);
        }
    }
}