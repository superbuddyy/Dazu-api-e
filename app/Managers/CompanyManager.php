<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Company;
use App\Services\ImageService;

class CompanyManager
{
    public function storeAvatar($file, Company $model)
    {
        $imageService = resolve(ImageService::class);
        $model->avatar = $imageService->store($file, Company::class);
        return $model->save();
    }

    public function storeVideoAvatar($videoAvatarLink, Company $model)
    {
        $model->video_avatar = $videoAvatarLink;
        return $model->save();
    }
}
