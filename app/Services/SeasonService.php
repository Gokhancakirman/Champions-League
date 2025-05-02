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
                    $this->seasons->updateMatchResult($match, $result);
                    $this->seasons->updateStandings($season, $match, $result);
                }
                
                $this->seasons->updateSeasonWeek($season);
                $this->seasons->makePassiveIfEnded($season);
                return response()->json([
                    'matches' => $this->seasons->loadMatchesWithRelations($matches)
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
                        $this->seasons->updateMatchResult($match, $result);
                        $this->seasons->updateStandings($season, $match, $result);
                        $allMatches[] = $match;
                    }
                    
                    $this->seasons->updateSeasonWeek($season);
                    $season = $season->fresh();
                }

                $this->seasons->makePassiveIfEnded($season);

                return response()->json([
                    'matches' => $allMatches,
                    'season' => $this->seasons->loadSeasonWithRelations($season)
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to simulate season: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'simulation' => 'Failed to simulate season. Please try again.'
            ]);
        }
    }

    public function reset(string $slug)
    {
        try {
            return \DB::transaction(function () use ($slug) {
                $season = $this->seasons->getSeasonWithSlug($slug);
                $this->seasons->resetSeason($season);
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