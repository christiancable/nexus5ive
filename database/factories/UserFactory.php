<?php

namespace Database\Factories;

use App\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->username,
            'email' => $this->faker->email,
            'password' => Str::random(10),
            'remember_token' => Str::random(10),
            'theme_id' => function () {
                return Theme::firstOrFail()->id;
            },
            'email_verified_at' => Carbon::now(),
        ];
    }
}
