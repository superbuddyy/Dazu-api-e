<?php

namespace App\Http\Resources\RecentSearch;

use Illuminate\Http\Resources\Json\JsonResource;

class RecentSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return array_merge(
            parent::toArray($request),
            [
                'id' => $this->id,
                'display_name' => $this->display_name ?? null,
                'lat' => $this->lat ?? null,
                'lon' => $this->lon ?? null,
                'osm_id' => $this->id
                // 'created_at' => $this->created_at->format('Y-m-d H:i:s') ?? null,
                // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s') ?? null,
            ]
        );
    }
}
