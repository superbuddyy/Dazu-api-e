<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\AttributeType;
use App\Http\Requests\Search\SearchRequest;
use App\Http\Resources\Offer\OfferCollection;
use App\Managers\CategoryManager;
use App\Models\Attribute;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController
{
    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * SearchService constructor.
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(SearchRequest $request)
    {
        $offers = $this->searchService->search($request->except(['page']));
        return response()->success(new OfferCollection($offers));
    }

    public function getFilters(Request $request)
    {
        $typeAttribute = Attribute::where('slug', 'typ')->first();
        $types = $typeAttribute->options->map(function ($option) {
            return [
                'name' => $option->name,
                'slug' => $option->slug,
            ];
        })->toArray();

        $floorAttribute = Attribute::where('slug', 'pietro')->first();
        $floors = $floorAttribute->options->map(function ($option) {
            return [
                'name' => $option->name,
                'slug' => $option->slug,
            ];
        })->toArray();

        $roomsAttribute = Attribute::where('slug', 'ilosc-pokojow')->first();
        $rooms = $roomsAttribute->options->map(function ($option) {
            return [
                'name' => $option->name,
                'slug' => $option->slug,
            ];
        })->toArray();


        $categories = Cache::rememberForever('categories-tree', function () {
            return resolve(CategoryManager::class)->getCategoryTree(null, '', 'children', true);
        });

        $priceFilterValues = Cache::rememberForever('price-filters', function () {
            return $this->searchService->getPriceFiltersValues();
        });

        $surfaceFilterValues = Cache::rememberForever('surface-filters', function () {
            return $this->searchService->getSurfaceFiltersValues();
        });

        $attributes = $this->searchService->getAttributes();

        return [
            'categories' => $categories,
            'types' => $types,
            'price' => $priceFilterValues,
            'attributes' => [
                'metraz' => $surfaceFilterValues,
                'pietro' => $floors,
                'ilosc-pokojowpomieszczen' => $rooms,
            ],
            'attributes2' => $attributes
        ];
    }
}
