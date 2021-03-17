<?php

declare(strict_types=1);

/** @var Factory $factory */

use App\Enums\TransactionStatus;
use App\Models\Post;
use App\Models\Transaction;
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
$factory->define(Transaction::class, function (Faker $faker): array {
    return [
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'description' => 'Pakiet Gold',
        'code' => $faker->swiftBicNumber,
        'address' => $faker->address,
        'status' => TransactionStatus::PENDING,
        'line_items' => [
            [
                'description' => 'Pakiet Gold',
                'unit' => 'szt.',
                'qty' => '1',
                'price' => '1000',
            ],
        ],
        'total' => '1000',
        'user_id' => User::inRandomOrder()->first()->id,
    ];
});
