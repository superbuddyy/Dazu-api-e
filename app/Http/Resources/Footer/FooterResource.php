<?php

declare(strict_types=1);

namespace App\Http\Resources\Footer;

use App\Http\Resources\Attributes\AttributeValueResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
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
            parent::toArray($request),
            [
                'id' => $this->id ?? null,
                'title' => $this->title ?? null,
                'name' => $this->slug ?? null,
                'content' => $this->content ?? null,
            ]
        );
    }
}
