<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\FaqItem;

class FaqManager
{
    public function getList()
    {
        return FaqItem::all();
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
            ]
        );
    }

    public function delete($id)
    {
        $faqItem = $this->getItem($id);
        return $faqItem->delete();
    }
}
