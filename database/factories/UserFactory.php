<?php

namespace Database\Factories;

use App\User;
use App\Theme;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

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
            'email_verified_at' => Carbon::now()
        ];
    }
}
