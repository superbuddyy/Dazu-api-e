<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Popup\PopupResource;
use App\Models\Popup;

class PopupController extends Controller
{
    public function show(Popup $popup)
    {
        return response()->success(new PopupResource($popup));
    }
}
