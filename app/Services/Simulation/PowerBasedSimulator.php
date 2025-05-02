<?php

namespace App\Services\Simulation;

use App\Models\Fixture;

class PowerBasedSimulator implements IMatchSimulator
{
    /**
     * Maximum home advantage multiplier based on supporter strength (15% boost).
     */
    private const MAX_HOME_ADVANTAGE = 1.15;

    /**
     * Maximum away team boost (3% boost).
     */
    private const MAX_AWAY_BOOST = 1.03;

    /**
     * Base goal factor to scale team power into expected goals.
     * Adjust to tune average goals per match.
     */
    private const GOAL_FACTOR = 3.0;

    /**
     * Form impact on team performance (up to 10% boost or reduction)
     */
    private const MAX_FORM_IMPACT = 0.10;

    /**
     * Fatigue impact range (random value between 0.85 and 1.0)
     */
    private const MIN_FATIGUE_FACTOR = 0.85;
    private const MAX_FATIGUE_FACTOR = 1.0;

    /**
     * Weather types and their probabilities
     */
    private const WEATHER_TYPES = [
        'sunny' => 0.60,    // 60% chance
        'rainy' => 0.25,    // 25% chance
        'very_rainy' => 0.10, // 10% chance
        'snowy' => 0.05     // 5% chance
    ];

    /**
     * Goal factors for different weather conditions
     */
    private const WEATHER_GOAL_FACTORS = [
        'sunny' => 2.5,
        'rainy' => 2.0,
        'very_rainy' => 1.5,
        'snowy' => 1.0
    ];

    /**
     * Determine the weather condition for the match
     */
    private function determineWeather(): string
    {
        $random = mt_rand() / mt_getrandmax();
        $cumulativeProbability = 0;

        foreach (self::WEATHER_TYPES as $weather => $probability) {
            $cumulativeProbability += $probability;
            if ($random <= $cumulativeProbability) {
                return $weather;
            }
        }

        return 'sunny'; // fallback
    }

    /**
     * Get the goal factor based on weather conditions
     */
    private function getWeatherAdjustedGoalFactor(string $weather): float
    {
        return self::WEATHER_GOAL_FACTORS[$weather] ?? self::GOAL_FACTOR;
    }

    /**
     * Apply power-based reduction to the goal factor
     */
    private function applyPowerReduction(float $goalFactor, float $teamPower): float
    {
        if ($teamPower < 65) {
            // Weak teams: reduce by 50%
            return $goalFactor * 0.6;
        } elseif ($teamPower < 80) {
            // Average teams: reduce by 20%
            return $goalFactor * 0.8;
        }
        // Strong teams: no reduction
        return $goalFactor;
    }

    /**
     * Calculate form factor based on recent results
     */
    private function calculateFormFactor(Fixture $fixture, bool $isHomeTeam): float
    {
        $team = $isHomeTeam ? $fixture->homeTeam : $fixture->awayTeam;
        $recentMatches = $team->fixtures()
            ->whereNotNull('home_team_score')
            ->whereNotNull('away_team_score')
            ->orderBy('week', 'desc')
            ->take(5)
            ->get();

        if ($recentMatches->isEmpty()) {
            return 1.0;
        }

        $formPoints = 0;
        foreach ($recentMatches as $match) {
            $isHome = $match->home_team_id === $team->id;
            $goalsFor = $isHome ? $match->home_team_score : $match->away_team_score;
            $goalsAgainst = $isHome ? $match->away_team_score : $match->home_team_score;
            
            if ($goalsFor > $goalsAgainst) {
                $formPoints += 3;
            } elseif ($goalsFor === $goalsAgainst) {
                $formPoints += 1;
            }
        }

        $maxPoints = $recentMatches->count() * 3;
        $formPercentage = $formPoints / $maxPoints;
        
        // Convert form percentage to impact (-10% to +10%)
        return 1.0 + (($formPercentage - 0.5) * 2 * self::MAX_FORM_IMPACT);
    }

    /**
     * Calculate fatigue factor using random value
     */
    private function calculateFatigueFactor(Fixture $fixture, bool $isHomeTeam): float
    {
        return mt_rand(
            (int)(self::MIN_FATIGUE_FACTOR * 100),
            (int)(self::MAX_FATIGUE_FACTOR * 100)
        ) / 100;
    }

    public function simulate(Fixture $fixture, Bool $prediction = false): array
    {
        // Determine weather condition
        $weather = $this->determineWeather();
        $weatherGoalFactor = $this->getWeatherAdjustedGoalFactor($weather);

        // Retrieve power ratings and supporter strengths
        $homePower = $fixture->homeTeam->power;
        $awayPower = $fixture->awayTeam->power;
        $homeSupporterStrength = $fixture->homeTeam->supporter_strength;
        $awaySupporterStrength = $fixture->awayTeam->supporter_strength;

        // Calculate home advantage based on supporter strength
        $homeAdvantage = 1.0 + (($homeSupporterStrength / 100) * (self::MAX_HOME_ADVANTAGE - 1.0));
        $awayBoost = 1.0 + (($awaySupporterStrength / 100) * (self::MAX_AWAY_BOOST - 1.0));

        // Calculate additional factors
        $homeFormFactor = $this->calculateFormFactor($fixture, true);
        $awayFormFactor = $this->calculateFormFactor($fixture, false);
        $homeFatigueFactor = $this->calculateFatigueFactor($fixture, true);
        $awayFatigueFactor = $this->calculateFatigueFactor($fixture, false);

        // Apply power-based reduction to weather-adjusted goal factors
        $homeGoalFactor = $this->applyPowerReduction($weatherGoalFactor, $homePower);
        $awayGoalFactor = $this->applyPowerReduction($weatherGoalFactor, $awayPower);

        // Calculate expected goals with all factors
        $lambdaHome = max(0.1, ($homePower / 100) * $homeAdvantage * $homeGoalFactor * 
            $homeFormFactor * $homeFatigueFactor);
        $lambdaAway = max(0.1, ($awayPower / 100) * $awayBoost * $awayGoalFactor * 
            $awayFormFactor * $awayFatigueFactor);

        // Sample goals
        $homeGoals = $this->samplePoisson($lambdaHome);
        $awayGoals = $this->samplePoisson($lambdaAway);

        // Store simulation details
        if ($prediction === false) {
            $simulationDetails = [
                'home_team' => [
                    'power' => $homePower,
                    'supporter_strength' => $homeSupporterStrength,
                    'supporter_advantage' => $homeAdvantage,
                    'form_factor' => $homeFormFactor,
                    'fatigue_factor' => $homeFatigueFactor,
                    'expected_goals' => $lambdaHome,
                    'actual_goals' => $homeGoals
                ],
                'away_team' => [
                    'power' => $awayPower,
                    'supporter_strength' => $awaySupporterStrength,
                    'supporter_boost' => $awayBoost,
                    'form_factor' => $awayFormFactor,
                    'fatigue_factor' => $awayFatigueFactor,
                    'expected_goals' => $lambdaAway,
                    'actual_goals' => $awayGoals
                ],
                'weather' => [
                    'condition' => $weather,
                    'goal_factor' => $weatherGoalFactor
                ],
                'simulation_timestamp' => now()->toIso8601String()
            ];

            $fixture->simulation_details = $simulationDetails;
            $fixture->save();
        }

        return [
            'home_team_score' => $homeGoals,
            'away_team_score' => $awayGoals,
        ];
    }

    /**
     * Poisson-distributed random sample using inversion by multiplication.
     *
     * @param float $lambda
     * @return int
     */
    private function samplePoisson(float $lambda): int
    {
        $L = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            // mt_rand() / mt_getrandmax() yields uniform [0,1)
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $L);

        return $k - 1;
    }
} 