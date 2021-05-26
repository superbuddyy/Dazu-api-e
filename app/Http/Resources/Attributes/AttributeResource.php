<?php

declare(strict_types=1);

namespace App\Http\Resources\Attributes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'id' => $this->id ?? null,
            'name' => $this->name ?? null,
            'slug' => $this->slug ?? null,
            'type' => $this->type ?? null,
            'unit' => $this->unit ?? null,
            'options' => $this->options ?? null,
            'offer_types' => $this->offer_types ?? null,
        ];
    }
}
