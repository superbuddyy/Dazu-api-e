<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Models\Popup;
use App\Models\User;
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
$factory->define(Popup::class, function (Faker $faker): array {
    $num = rand(1, 7);
    $imageService = resolve(ImageService::class);
    $file = new File(storage_path('seed/offers/ad_image_' . $num . '.jpg'));
    return [
        'title' => $faker->realText(10),
        'content' => $faker->realText(100),
        'image' => $imageService->store($file, Popup::class),
        'show_again_after' => 1,
        'is_active' => true,
    ];
});
