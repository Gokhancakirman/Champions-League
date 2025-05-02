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
}