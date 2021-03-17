<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Enums\OfferType;
use App\Models\Offer;
use App\Models\User;
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

$category = function (?bool $isActive = null): Category {
    if ($isActive !== null) {
        return Category::where('is_active', $isActive)->whereIsLeaf()->inRandomOrder()->first() ?:
            factory(Category::class)->create(['is_active' => $isActive]);
    }

    return Category::whereIsLeaf()->inRandomOrder()->first() ?: factory(Category::class)->create();
};

$factory->define(Offer::class, function (Faker $faker) use ($category): array {
    return [
        'title' => $faker->realText(20),
        'slug' => null,
        'description' => $faker->realText(100),
        'price' => $faker->randomNumber(4),
        'lat' => 0,
        'lon' => 0,
        'location_name' => 'Warszawa, Mazowieckie',
        'category_id' => $category()->id,
        'links' => [],
        'refresh_count' => 0,
        'user_id' => factory(User::class)->create()->id,
    ];
});
