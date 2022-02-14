<?php

declare(strict_types=1);

namespace App\Http\Resources\Base;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;

class ResourceCollection extends BaseResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param Request|FormRequest $request
     * @return array
     */
    public function toArray($request): array
    {
        return array_merge(
            Paginator::make($this->resource)->resolve(),
            ['data' => $this->collection]
        );
    }
}
