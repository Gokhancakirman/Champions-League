<?php

namespace App\Services\Prediction;

use App\Models\Season;
use App\Services\Simulation\IMatchSimulator;

/**
 * Monte Carlo predictor using the match simulator to forecast outcomes.
 */
class ChampionshipPredictor implements IPredictor
{
    public function __construct(
        protected IMatchSimulator $simulator
    ) {}

    public function predict(Season $season, int $simulations = 100): array
    {
        // Load remaining fixtures and current standings
        $remaining = $season->fixtures()
            ->where('played', false)
            ->orderBy('week')
            ->get();

        $initialStandings = [];
        foreach ($season->standings as $standing) {
            $initialStandings[$standing->team_id] = [
                'points'          => $standing->points,
                'goal_difference' => $standing->goals_for - $standing->goals_against,
            ];
        }

        // Initialize win counters
        $wins = array_fill_keys(array_keys($initialStandings), 0.0);

        for ($i = 0; $i < $simulations; $i++) {
            // Clone standings for simulation
            $simStand = $initialStandings;

            // Simulate each fixture
            foreach ($remaining as $fixture) {
                ['home_team_score' => $hg, 'away_team_score' => $ag] = $this->simulator->simulate($fixture, true);
                $home = $fixture->home_team_id;
                $away = $fixture->away_team_id;

                // Points
                if ($hg > $ag) {
                    $simStand[$home]['points'] += 3;
                } elseif ($hg < $ag) {
                    $simStand[$away]['points'] += 3;
                } else {
                    $simStand[$home]['points'] += 1;
                    $simStand[$away]['points'] += 1;
                }

                // Goal difference
                $simStand[$home]['goal_difference'] += ($hg - $ag);
                $simStand[$away]['goal_difference'] += ($ag - $hg);
            }

            // Determine champion(s)
            // 1) Max points
            $maxPts = max(array_column($simStand, 'points'));
            $candidates = array_keys(array_filter(
                $simStand,
                fn($s) => $s['points'] === $maxPts
            ));

            if (count($candidates) > 1) {
                // 2) Tiebreak by goal difference
                $gds = array_map(fn($tid) => $simStand[$tid]['goal_difference'], $candidates);
                $maxGd = max($gds);
                $winners = array_filter(
                    $candidates,
                    fn($tid) => $simStand[$tid]['goal_difference'] === $maxGd
                );
            } else {
                $winners = $candidates;
            }

            // Distribute win shares
            $share = 1 / count($winners);
            foreach ($winners as $tid) {
                $wins[$tid] += $share;
            }
        }

        // Convert counts to percentage
        $results = [];
        foreach ($wins as $tid => $count) {
            $results[$tid] = ($count / $simulations) * 100;
        }

        return $results;
    }
} 