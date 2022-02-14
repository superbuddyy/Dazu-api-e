<?php

declare(strict_types=1);

namespace App\Http\Resources\Base;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Paginator extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request|FormRequest $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->items(),
            'current_page' => $this->currentPage(),
            'first_page' => 1,
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'first_page_url' => $this->url(1),
            'last_page_url' => $this->url($this->lastPage()),
            'path' => $this->resolveCurrentPath(),
        ];
    }
}
