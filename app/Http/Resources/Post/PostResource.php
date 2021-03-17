<?php

declare(strict_types=1);

namespace App\Http\Resources\Post;

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
                'slug' => $this->slug ?? null,
                'content' => $this->content ?? null,
                'main_photo' => $this->main_photo ?? null,
                'main_photo_url' => $this->mainPhotoUrl ?? null,
                'created_at' => $this->created_at->format('Y-m-d H:II') ?? null,
                'updated_at' => $this->updated_at->format('Y-m-d H:II') ?? null,
                'user' => [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                ],
            ]
        );
    }
}
