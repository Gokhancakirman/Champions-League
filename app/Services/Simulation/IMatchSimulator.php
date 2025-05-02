<?php

namespace App\Services\Simulation;

use App\Models\Fixture;

/**
 * Interface for match simulation strategies.
 */
interface IMatchSimulator
{
    /**
     * Simulate a single fixture, returning scores.
     *
     * @param Fixture $fixture
     * @return array{home_team_score:int, away_team_score:int}
     */
    public function simulate(Fixture $fixture): array;
} 