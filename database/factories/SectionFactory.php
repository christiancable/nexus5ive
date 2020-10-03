<?php

namespace Database\Factories;

use App\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Section::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'intro' => $this->faker->paragraph,
            'user_id' => $this->faker->randomDigitNotNull,
            'parent_id' => $this->faker->randomDigitNotNull,
        ];
    }
}
