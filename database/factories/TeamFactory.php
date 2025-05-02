<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'logo' => $this->faker->imageUrl(100, 100),
            'power_min' => $this->faker->numberBetween(70, 80),
            'power_max' => $this->faker->numberBetween(80, 100),
            'supporter_strength' => $this->faker->numberBetween(50, 100),
        ];
    }
} 