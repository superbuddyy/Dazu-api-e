<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Popup\PopupResource;
use App\Managers\PopupManager;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PopupController extends Controller
{
    private PopupManager $popupManager;

    public function __construct(PopupManager $popupManager)
    {
        $this->popupManager = $popupManager;
    }

    public function show(Popup $popup)
    {
        return response()->success(new PopupResource($popup));
    }

    public function store(Request $request)
    {
        $popup = $this->popupManager->store($request->all());

        if ($request->has('image')) {
            $this->popupManager->storeImage($request->file('image'), $popup);
        }

        return response()->success($popup, Response::HTTP_CREATED);
    }

    public function update(Request $request, Popup $popup)
    {
        $this->popupManager->update($request->all(), $popup);

        if ($request->has('image')) {
            $this->popupManager->storeImage($request->file('image'), $popup);
        }

        return response()->success($popup, Response::HTTP_CREATED);
    }
}
