<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonTeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'power' => $this->faker->numberBetween(70, 90),
            'supporter_strength' => $this->faker->numberBetween(50, 100),
        ];
    }
} 