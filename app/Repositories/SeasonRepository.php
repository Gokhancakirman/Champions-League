<?php

namespace App\Repositories;

use App\Models\Season;

class SeasonRepository
{
    public function hasActiveSeason(): bool
    {
        // Active if current_week <= total_weeks
        return Season::where('is_active', true)->exists();
    }

    public function getActiveSeason(): Season | null
    {
        return Season::where('is_active', true)->first();
    }
    
    public function getSeasonWithSlug(string $slug): Season
    {
        $season = Season::with([
            'teams',
            'fixtures' => function($query) {
                $query->with(['homeTeam.team', 'awayTeam.team'])
                      ->orderBy('week');
            },
            'standings' => function($query) {
                $query->with('seasonTeam.team')
                      ->orderBy('points', 'desc')
                      ->orderBy('goal_difference', 'desc');
            }
        ])->where('slug', $slug)->firstOrFail();

        // Group fixtures by week after fetching
        $season->setRelation('fixtures', $season->fixtures->groupBy('week'));
        
        return $season;
    }

    public function makePassiveIfEnded(Season $season): void
    {
        if ($season->current_week > $season->total_weeks) {
            $season->is_active = false;
            $season->save();
        }
    }

    public function create(array $data): Season
    {
        return Season::create($data);
    }

    public function addTeams(Season $season, array $teams): void
    {
        foreach ($teams as $team) {
            $season->teams()->create([
                'team_id' => $team['team_id'],
                'power' => $team['power'],
                'supporter_strength' => $team['supporter_strength']
            ]);
        }
    }

    public function addFixtures(Season $season, array $fixtures): void
    {
        $season->fixtures()->createMany($fixtures);
    }

    public function addStandings(Season $season, array $standingsData): void
    {
        $season->standings()->createMany($standingsData);
    }

    public function updateSeasonWeeks(Season $season, int $teamCount): void
    {
        $season->total_weeks = ($teamCount - 1) * 2; // Standard season length
        $season->save();
    }

    public function updateMatchResult($match, $result): void
    {
        $match->update([
            'home_team_score' => $result['home_team_score'],
            'away_team_score' => $result['away_team_score'],
            'played' => true
        ]);
    }

    public function updateSeasonWeek(Season $season): void
    {
        $season->current_week++;
        $season->save();
    }

    public function loadMatchesWithRelations($matches)
    {
        return $matches->load(['homeTeam', 'awayTeam']);
    }

    public function loadSeasonWithRelations(Season $season)
    {
        return $season->load('standings.seasonTeam.team');
    }

    public function updateTeamStats($standing, $goalsFor, $goalsAgainst): void
    {
        $standing->update([
            'played' => $standing->played + 1,
            'goals_for' => $standing->goals_for + $goalsFor,
            'goals_against' => $standing->goals_against + $goalsAgainst
        ]);
    }

    public function updateStandings($season, $match, $result): void
    {
        $homeTeamStanding = $season->standings->where('team_id', $match->home_team_id)->first();
        $awayTeamStanding = $season->standings->where('team_id', $match->away_team_id)->first();
        
        $homeScore = $result['home_team_score'];
        $awayScore = $result['away_team_score'];

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

    public function resetSeason(Season $season): void
    {
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
    }

    public function getAllSeasonsWithWinners()
    {
        $seasons = Season::with(['standings.seasonTeam.team'])
            ->orderBy('created_at', 'desc')
            ->get();

        $seasons->each(function ($season) {
            $standing_data = $season->standings->sortByDesc('points')->first();
            if ($standing_data && $standing_data->seasonTeam) {
                $season->winner = $standing_data->seasonTeam->team;
            }
        });

        return $seasons;
    }

    public function getActiveSeasonWithRelations()
    {
        return Season::with(['standings.seasonTeam.team'])
            ->where('is_active', true)
            ->first();
    }
}