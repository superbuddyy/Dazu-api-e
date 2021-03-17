<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Offer\OfferCollection;
use App\Managers\FavoriteManager;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavoriteController
{
    /**
     * @var FavoriteManager
     */
    protected $favoriteManager;

    public function __construct(FavoriteManager $favoriteManager)
    {
        $this->favoriteManager = $favoriteManager;
    }

    public function index(Request $request): JsonResponse
    {
        $favoriteOffers = $this->favoriteManager->getList();
        return response()->success(new OfferCollection($favoriteOffers, true));
    }

    public function store(Request $request, Offer $offer): JsonResponse
    {
        $result = $this->favoriteManager->store($offer);

        return response()->success($result);
    }

    public function activateNotifications(Offer $offer): Response
    {
        $this->favoriteManager->updateNotifications($offer, true);

        return response()->noContent();
    }

    public function deactivateNotifications(Offer $offer): Response
    {
        $this->favoriteManager->updateNotifications($offer, false);

        return response()->noContent();
    }

    public function destroy(Request $request, Offer $offer): Response
    {
        if (!$this->favoriteManager->delete($offer)) {
            return response()->error('fail_to_delete', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->noContent();
    }
}
