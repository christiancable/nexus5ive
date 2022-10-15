<?php

namespace Database\Factories;

use App\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'path' => $this->faker->url,
            'name' => $this->faker->unique()->word,
        ];
    }
}
