<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Popup\PopupResource;
use App\Managers\PopupManager;
use App\Models\Popup;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupController extends Controller
{
    private PopupManager $popupManager;

    public function __construct(PopupManager $popupManager)
    {
        $this->popupManager = $popupManager;
    }

    public function index()
    {
        $list = $this->popupManager->getList();
        return response()->success($list->map(fn ($item) => new PopupResource($item)));
    }

    public function show(Popup $popup)
    {
        return response()->success(new PopupResource($popup));
    }
}
