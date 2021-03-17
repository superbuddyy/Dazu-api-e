<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Models\Offer;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\User;
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
$factory->define(AttributeOption::class, function (Faker $faker): array {
    return [
        'name' => $faker->realText(5),
        'slug' => $faker->realText(20),
        'attribute_id' => factory(Attribute::class)->create()->id,
    ];
});
