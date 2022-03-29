<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\FaqItem;
use App\Services\ImageService;

class FaqManager
{
    public function getList()
    {
        return FaqItem::all();
    }

    public function getAll() {
        $data = FaqItem::all();
        foreach ($data as $key) {
            $key['content'] = html_entity_decode($key['content']);
        }
        return $data;
        // return FaqItem::orderBy('id', 'ASC')->get();
    }

    public function getItem($id)
    {
        return FaqItem::findOrFail($id);
    }

    public function updateOrCreate(array $faqItemData)
    {
        return FaqItem::updateOrCreate(
            [
                'id' => $faqItemData['id']
            ],
            [
                'title' => $faqItemData['title'],
                'content' => $faqItemData['content'],
                'file' => $faqItemData['file'],
            ]
        );
    }

    public function delete($id)
    {
        $faqItem = $this->getItem($id);
        return $faqItem->delete();
    }
    public function uploadFaqFile($file) {
        $imageService = resolve(ImageService::class);
        $filename = $imageService->store($file, FaqItem::class);
        $data = config('app.url') . '/storage/other/' . $filename;
        return ["url"=>$data, "filename" => $filename];
    }
}
