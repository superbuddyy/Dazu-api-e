<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Models\User;
use App\Models\UserProfile;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------UserProfileManager
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(UserProfile::class, function (Faker $faker): array {
    return [
        'name' => $faker->firstName,
        'phone' => $faker->phoneNumber,
        'city' => $faker->city,
        'street' => $faker->firstName,
        'zip_code' => $faker->postcode,
        'country' => $faker->country,
        'nip' => '',
        'user_id' => factory(User::class)->create()->id
    ];
});
