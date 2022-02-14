<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttributeType;
use App\Enums\OfferStatus;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SearchService
{
    public function search(
        array $searchArguments,
        bool $onlyVisible = true,
        string $orderBy = 'price',
        string $order = 'ASC'
    ): LengthAwarePaginator
    {
        $query = Offer::query();
        $perPage = Arr::pull($searchArguments, 'limit', 12);
        if ($onlyVisible) {
            $query->where('expire_time', '>', Carbon::now())
                ->where(function ($query) {
                    $query->where('visible_from_date', '<', Carbon::now())
                        ->orWhere('visible_from_date', null);
                })
                ->where('status', OfferStatus::ACTIVE);
        }

        $query = $this->buildQuery($searchArguments, $query);

        // $query->orderBy('raise_at', 'DESC');
        $query->orderBy(
            Arr::get($searchArguments, 'order_by', $orderBy),
            Arr::get($searchArguments, 'order', $order)
        );

        return $query->paginate($perPage);
    }

    public function getSurfaceFiltersValues(): array
    {
        $values = [];
        for ($i = 0; $i < 100; $i += 5) {
            array_push($values, $i);
        }

        for ($i = 100; $i < 200; $i += 10) {
            array_push($values, $i);
        }

        for ($i = 200; $i < 1000; $i += 100) {
            array_push($values, $i);
        }

        for ($i = 1000; $i < 10000; $i += 500) {
            array_push($values, $i);
        }

        for ($i = 10000; $i < 100000; $i += 10000) {
            array_push($values, $i);
        }

        for ($i = 100000; $i <= 500000; $i += 100000) {
            array_push($values, $i);
        }

        return ['min' => $values, 'max' => $values];
    }

    public function getPriceFiltersValues(): array
    {
        $values = [];
        for ($i = 50; $i < 1000; $i += 50) {
            array_push($values, $i);
        }

        for ($i = 1000; $i < 2000; $i += 100) {
            array_push($values, $i);
        }

        for ($i = 2000; $i < 5000; $i += 250) {
            array_push($values, $i);
        }

        for ($i = 5000; $i < 15000; $i += 500) {
            array_push($values, $i);
        }

        for ($i = 15000; $i < 500000; $i += 5000) {
            array_push($values, $i);
        }

        for ($i = 500000; $i < 1000000; $i += 50000) {
            array_push($values, $i);
        }

        for ($i = 1000000; $i <= 5000000; $i += 250000) {
            array_push($values, $i);
        }

        return ['min' => $values, 'max' => $values];
    }

    public function getAttributes()
    {
        // return Cache::rememberForever('attributes-filters', function () {
            return Attribute::orderBy('type', 'desc')->get()->map(function ($attr) {
                if ($attr->type === AttributeType::CHOICE || $attr->type === AttributeType::MULTI_CHOICE) {
                    return [
                        'id' => $attr->id,
                        'name' => $attr->name,
                        'slug' => $attr->slug,
                        'type' => $attr->type,
                        'unit' => $attr->unit,
                        'options' => $attr->options->map(function ($option) {
                            return ['id' => $option->id, 'name' => $option->name, 'slug' => $option->slug];
                        }),
                    ];
                }

                return [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'slug' => $attr->slug,
                    'type' => $attr->type,
                    'unit' => $attr->unit,
                    'options' => [],
                ];
            });
        // });
    }

    protected function buildQuery(array $params, Builder $query): Builder
    {
        foreach ($params as $paramName => $paramValue) {
            switch ($paramName) {
                case 'phrase':
                    $query->where('title', 'like', "%$paramValue%")
                        ->orWhere('location_name', 'like', "%$paramValue%");
                    break;
                case 'category':
                    // $category = Category::where('slug', $paramValue)->firstOrFail();
                    // $query->whereHas('category', function ($query) use ($category) {
                    //     return $query->where('_lft', '>=', $category->_lft)
                    //         ->where('_rgt', '<=', $category->_rgt);
                    // });
                    $ar_category = explode(",", $paramValue);
                    $category = Category::whereIn('slug', $ar_category)->get();
                    $query->whereHas('category', function ($query) use ($category) {
                        $ids = [];
                        foreach ($category as $key => $value) {
                            $ids[] = $value->id;
                        }
                        return $query->whereIn('id', $ids);
                    });
                    break;
                case 'price':
                    if (array_key_exists('min', $paramValue) && array_key_exists('max', $paramValue)) {
                        $query->whereBetween('price', [$paramValue['min'] * 100, $paramValue['max'] * 100]);
                    } elseif (array_key_exists('min', $paramValue)) {
                        $query->where('price', '>=', $paramValue['min'] * 100);
                    } elseif (array_key_exists('max', $paramValue)) {
                        $query->where('price', '<=', $paramValue['max'] * 100);
                    }
                    break;
                case 'search':
                    $query->where('id', 'like', "%$paramValue%")
                        ->orWhere('title', 'like', "%$paramValue%")
                        ->orWhereHas('user', function ($query) use ($paramValue) {
                            $query->where('email', 'like', "%$paramValue%");
                        });
                    break;
                case 'location':
                    $query->selectRaw("offers.*,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lon ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance", [$paramValue['lat'], $paramValue['lon'], $paramValue['lat']])
                        ->having("distance", "<", '10');
                    break;
                case 'dodatkowe':
                    foreach ($paramValue as $key => $value) {
                        if ($value === false) {
                            continue;
                        }
                        $query->whereHas('attributes', function ($query) use ($key, $paramName) {
                            $query->where('attributes.slug', $paramName)
                                ->where('attribute_value.value', $key);
                        });
                    }
                    break;
                default:
                    if (is_array($paramValue) && array_key_exists('min', $paramValue)) {
                        $query->whereHas('attributes', function ($query) use ($paramValue, $paramName) {
                            $query->where('attributes.slug', $paramName)
                                ->where('attribute_value.value', '>=', (int)$paramValue['min']);
                        });
                    } else if (is_array($paramValue) && array_key_exists('max', $paramValue)) {
                        $query->whereHas('attributes', function ($query) use ($paramValue, $paramName) {
                            $query->where('attributes.slug', $paramName)
                                ->where('attribute_value.value', '<=', (int)$paramValue['max']);
                        });
                    } else if (is_array($paramValue)) {
                        $i = 0;
                        foreach ($paramValue as $param) {
                            if ($i === 0) {
                                $query->whereHas('attributes', function ($query) use ($param, $paramName) {
                                    $query->where('attributes.slug', $paramName)
                                        ->where('attribute_value.value', $param);
                                });
                                $i++;
                            } else {
                                $query->orWhereHas('attributes', function ($query) use ($param, $paramName) {
                                    $query->where('attributes.slug', $paramName)
                                        ->where('attribute_value.value', $param);
                                });
                            }
                        }
                    } else {
                        $query->whereHas('attributes', function ($query) use ($paramValue, $paramName) {
                            $query->where('attributes.slug', $paramName)
                                ->where('attribute_value.value', $paramValue);
                        });
                    }
            }
        }
        return $query;
    }
}
