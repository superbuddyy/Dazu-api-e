<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Offer\OfferCollection;
use App\Managers\FavoriteFilterManager;
use App\Managers\FavoriteManager;
use App\Models\FavoriteFilter;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FavoriteFilterController
{
    /**
     * @var FavoriteFilterManager
     */
    protected $favoriteFilterManager;

    public function __construct(FavoriteFilterManager $favoriteFilterManager)
    {
        $this->favoriteFilterManager = $favoriteFilterManager;
    }

    public function index()
    {
        $result = $this->favoriteFilterManager->getList();
        return response()->success($result);
    }

    public function store(Request $request): JsonResponse
    {
        $filters = $request->filters;
        unset($filters['page']);

        $result = $this->favoriteFilterManager->store(
            $filters,
            $request->period,
            $request->notification
        );

        return response()->success($result, Response::HTTP_CREATED);
    }

    public function destroy(FavoriteFilter $filter): Response
    {
        if (!$this->favoriteFilterManager->delete($filter)) {
            return response()->error('fail_to_delete', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->noContent();
    }
}
