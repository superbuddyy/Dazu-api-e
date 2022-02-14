<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\AvatarType;
use App\Models\Avatar;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Symfony\Component\HttpFoundation\File\File;


$factory->define(Avatar::class, function (Faker $faker) {
    $num = rand(1, 3);
    $imageService = resolve(ImageService::class);
    $file = new File(storage_path('seed/avatars/avatar' . $num . '.jpg'));
    return [
        'file' => $imageService->store($file, Avatar::class),
        'is_active' => true,
        'expire_date' => Carbon::now(),
        'type' => AvatarType::PHOTO,
        'user_id' => factory(User::class)->create()->id
    ];
});
