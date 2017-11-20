<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function ($faker) {

   $defaultTheme = App\Theme::firstOrFail();

    return [
        'name' => $faker->name,
        'username' => $faker->unique()->username,
        'email' => $faker->email,
        'password' => str_random(10),
        'remember_token' => str_random(10),
        'theme_id' => $defaultTheme->id,
    ];
});



$factory->define(App\Section::class, function ($faker) {
    return [
        'title' => $faker->sentence,
        'intro' => $faker->paragraph,
        'user_id' => $faker->randomDigitNotNull,
        'parent_id' => $faker->randomDigitNotNull,
    ];
});


$factory->define(App\Topic::class, function ($faker) {
    return [
        'title' => $faker->sentence,
        'intro' => $faker->paragraph,
        'section_id' => $faker->randomDigitNotNull,
    ];
});

$factory->define(App\Post::class, function ($faker) {
    return [
        'title' => $faker->sentence,
        'text' => $faker->paragraph,
        'popname' => $faker->sentence,
        'time' => $faker->date,
        'user_id' => $faker->randomDigitNotNull,
        'topic_id' => $faker->randomDigitNotNull,
    ];
});

$factory->define(App\Comment::class, function ($faker) {
    return [
        'user_id' => $faker->randomDigitNotNull,
        'author_id' => $faker->randomDigitNotNull,
        'text' => $faker->sentence,
        'read' => false,
    ];
});

$factory->define(App\Theme::class, function ($faker) {
    return [
        'path' => $faker->url,
        'name' => $faker->unique()->word,
    ];
});
