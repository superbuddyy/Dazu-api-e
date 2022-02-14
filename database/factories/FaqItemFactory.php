<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Models\FaqItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

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
$factory->define(FaqItem::class, function (Faker $faker): array {
    return [
        'title' => $faker->realText(30) . '?',
        'content' => $faker->realText(400),
    ];
});
