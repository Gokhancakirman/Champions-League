<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StandingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'points' => 0,
        ];
    }
} 