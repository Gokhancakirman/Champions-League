<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'total_weeks' => 6,
            'current_week' => 1,
            'is_active' => true,
        ];
    }
} 