<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'text' => fake()->paragraph(),
            'popname' => fake()->sentence(),
            'time' => fake()->date(),
            'user_id' => fake()->randomDigitNotNull(),
            'topic_id' => fake()->randomDigitNotNull(),
        ];
    }
}
