<?php

namespace Database\Factories;

use App\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomDigitNotNull,
            'author_id' => $this->faker->randomDigitNotNull,
            'text' => $this->faker->sentence,
            'read' => false,
        ];
    }
}
