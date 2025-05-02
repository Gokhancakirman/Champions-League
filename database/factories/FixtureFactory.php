<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FixtureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'week' => $this->faker->numberBetween(1, 6),
            'home_team_score' => null,
            'away_team_score' => null,
            'played' => false,
            'simulation_details' => null,
        ];
    }
} 