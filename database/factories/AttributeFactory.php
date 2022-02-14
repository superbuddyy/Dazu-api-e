<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Enums\AttributeType;
use App\Enums\AttributeUnit;
use App\Models\Attribute;
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
$factory->define(Attribute::class, function (Faker $faker): array {
    return [
        'name' => $faker->realText(10),
        'description' => $faker->realText(20),
        'type' => $faker->randomElement(AttributeType::getValues()),
        'unit' => $faker->randomElement(AttributeUnit::getValues()),
    ];
});
