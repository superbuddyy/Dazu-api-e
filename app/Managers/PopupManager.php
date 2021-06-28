<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Popup;
use App\Services\ImageService;

class PopupManager
{
    public function store(array $popupData)
    {
        return Popup::create([
           'title' => $popupData['title'],
           'content' => $popupData['content']
        ]);
    }

    public function update(array $popupData, Popup $popup)
    {
        return $popup->update($popupData);
    }

    public function storeImage($file, Popup $popup)
    {
        $imageService = resolve(ImageService::class);
        $popup->image = $imageService->store($file, Popup::class);
        $popup->save();

        return $popup;
    }

    public function removeImage(Popup $popup)
    {
        $imageService = resolve(ImageService::class);
        return $imageService->delete($popup->file['path_name']);
    }
}

