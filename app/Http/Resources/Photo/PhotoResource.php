<?php

declare(strict_types=1);

namespace App\Http\Resources\Photo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
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
            'file' => $this->file ?? null,
            'description' => $this->description ?? null,
            'position' => $this->position ?? null,
            'photo_url' => $this->photoUrl ?? null
        ];
    }
}
