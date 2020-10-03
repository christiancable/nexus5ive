<?php

namespace Database\Factories;

use App\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Topic::class;

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
            'section_id' => $this->faker->randomDigitNotNull,
        ];
    }
}
