<?php

namespace App\Http\Resources\RecentSearch;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecentSearchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }
    public function toArray($request): array
    {
        $this->collection->transform(function ($ad) {
            return (new RecentSearchResource($ad));
        });

        return parent::toArray($request);
    }
}
