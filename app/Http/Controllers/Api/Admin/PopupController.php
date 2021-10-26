<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Popup\PopupResource;
use App\Managers\PopupManager;
use App\Models\Popup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psy\Util\Json;

class PopupController extends Controller
{
    private PopupManager $popupManager;

    public function __construct(PopupManager $popupManager)
    {
        $this->popupManager = $popupManager;
    }

    public function show(Popup $popup): JsonResponse
    {
        return response()->success(new PopupResource($popup));
    }

    public function update(Request $request, Popup $popup): JsonResponse
    {
        $this->popupManager->update($request->all(), $popup);


        if ($request->has('image')) {
            try{
                $this->popupManager->removeImage($popup);    
                $this->popupManager->storeImage($request->file('image'), $popup);
            } catch (\Exception $ex) {

            }
            
        }

        return response()->success($popup, Response::HTTP_OK);
    }

    public function activate(Popup $popup): JsonResponse
    {
        $popup->is_active = true;
        return response()->success($popup->save());
    }

    public function deactivate(Popup $popup): JsonResponse
    {
        $popup->is_active = false;
        return response()->success($popup->save());
    }
}
