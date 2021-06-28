<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
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

$factory->define(Offer::class, function (Faker $faker): array {
    return [
        'title' => $faker->realText(20),
        'slug' => null,
        'status' => OfferStatus::ACTIVE,
        'description' => $faker->realText(100),
        'price' => $faker->randomNumber(4),
        'lat' => 0,
        'lon' => 0,
        'location_name' => 'Warszawa, Mazowieckie',
        'category_id' => Category::inRandomOrder()->first()->id,
        'links' => [],
        'refresh_count' => 0,
        'expire_time' => Carbon::now()->addDays(30),
        'raise_at' => null,
        'note' => null,
        'visible_from_date' => null,
        'user_id' => factory(User::class)->create()->id,
    ];
});
