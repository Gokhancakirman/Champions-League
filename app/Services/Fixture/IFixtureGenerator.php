<?php

namespace App\Services\Fixture;

use Illuminate\Support\Collection;

/**
 * Interface for fixture generators.
 */
interface IFixtureGenerator
{
    /**
     * Generate fixtures for the given collection of season teams.
     *
     * @param Collection $teams  Collection of SeasonTeam models (with id property).
     * @return array             List of fixtures: [ ['week'=>int, 'home_id'=>int, 'away_id'=>int], ... ]
     */
    public function generate(Collection $teams): array;
}
