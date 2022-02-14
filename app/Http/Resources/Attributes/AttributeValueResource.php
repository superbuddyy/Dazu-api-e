<?php

declare(strict_types=1);

namespace App\Http\Resources\Attributes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueResource extends JsonResource
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
            'description' => $this->description ?? null,
            'type' => $this->type ?? null,
            'unit' => $this->unit ?? null,
            'value' => $this->pivot->value ?? null,
            'result' => $this->result ?? $this->pivot->value ?? null,
        ];
    }
}
