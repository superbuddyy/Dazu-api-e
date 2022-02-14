<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Models\Offer;
use App\Models\Photo;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImageService;
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
$factory->define(Photo::class, function (Faker $faker): array {
    $num = rand(1, 7);
    $imageService = resolve(ImageService::class);
    $file = new File(storage_path('seed/offers/ad_image_' . $num . '.jpg'));
    return [
        'position' => $faker->randomNumber,
        'file' => $imageService->store($file, Photo::class),
        'path' => storage_path('seed/offers/ad_image_' . $num . '.jpg'),
        'offer_id' => factory(Offer::class)->create()->id,
    ];
});
