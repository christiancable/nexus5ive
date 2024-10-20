<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => fake()->randomDigitNotNull(),
            'author_id' => fake()->randomDigitNotNull(),
            'text' => fake()->sentence(),
            'read' => false,
        ];
    }
}
