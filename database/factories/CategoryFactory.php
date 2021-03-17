<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Models\Category;
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
$factory->define(Category::class, function (Faker $faker): array {
    return [
        'name' => $faker->realText(20),
        'slug' => null,
        'parent_id' => factory(Category::class)->create()->id,
    ];
});
