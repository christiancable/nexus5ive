<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ModeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'welcome' => fake()->paragraph(),
            // theme_id
            'active' => false,
            'override' => false,
        ];
    }
}
