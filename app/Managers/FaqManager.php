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
}
