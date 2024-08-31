<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'text' => $this->faker->paragraph,
            'popname' => $this->faker->sentence,
            'time' => $this->faker->date,
            'user_id' => $this->faker->randomDigitNotNull,
            'topic_id' => $this->faker->randomDigitNotNull,
        ];
    }
}
