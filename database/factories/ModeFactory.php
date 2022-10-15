<?php

namespace Database\Factories;

use App\Mode;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'welcome' => $this->faker->paragraph,
            // theme_id
            'active' => false,
            'override' => false,
        ];
    }
}
