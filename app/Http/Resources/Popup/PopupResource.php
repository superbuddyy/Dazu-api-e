<?php

declare(strict_types=1);

namespace App\Http\Resources\Popup;

use App\Http\Resources\Attributes\AttributeValueResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PopupResource extends JsonResource
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
            'title' => $this->title ?? null,
            'content' => $this->content ?? null,
            'image' => $this->file ?? null,
            'showAgainAfter' => $this->show_again_after ?? null,
            'is_active' => $this->is_active ?? null,
        ];
    }
}
