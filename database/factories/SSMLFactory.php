<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Ssml;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Ssml::class, function (Faker $faker) {
    return [
        'title' => $faker->company,
        'link' => $faker->url,
        'mp3' => $faker->url,
        'html' => '<p>some content</p>',
        'content' => '<p>some content</p>',
    ];
});
