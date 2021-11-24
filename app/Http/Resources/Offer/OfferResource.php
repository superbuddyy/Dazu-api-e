<?php

declare(strict_types=1);

namespace App\Http\Resources\Offer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request|FormRequest $request
     * @return array
     */
    public function toArray($request): array
    {
        if (Auth::user()) {
            $favorite = $this->getFavorite((string)Auth::id());
            $isFavorite = $favorite ? true : false;
        }

        $refreshPrice = 0;
        $sub = $this->activeSubscription;
        if ($this->refresh_count >= $sub->number_of_refreshes) {
            $refreshPrice = $sub->refresh_price;
        }

        $raisePrice = 0;
        if ($this->raise_count >= $sub->number_of_raises) {
            $raisePrice = $sub->raise_price;
        }

        return array_merge(
            parent::toArray($request),
            [
                'id' => $this->id ?? null,
                'title' => $this->title ?? null,
                'description' => $this->description ?? null,
                'slug' => $this->slug ?? null,
                'price' => $this->price ?? null,
                'old_price' => $this->price ?? null,
                'main_photo' => $this->main_photo ?? null,
                'photos' => $this->photos ?? null,
                'project_plan_photos' => $this->photos ?? null,
                'status' => $this->status ?? null,
                'links' => $this->links ?? null,
                'is_favorite' => $isFavorite ?? false,
                'is_expired' => $this->isExpired ?? false,
                'created_at' => $this->created_at->format('Y-m-d H:II') ?? null,
                'type' => $this->attributes->filter(function ($attribute) {
                    return $attribute->id === 1;
                })->first()->result ?? '',
                'is_with_bills' => $this->attributes->filter(function ($attribute) {
                    return $attribute->id === 7 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'is_argent' => $this->attributes->filter(function ($attribute) {
                    return $attribute->id === 20 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'location_name' => $this->location_name,
                'refresh_price' => $refreshPrice ?? null,
                'raise_price' => $raisePrice ?? null,
                'is_promoted' => $this->activeSubscription->id > 1 ?? false,
                'user_name' => $this->user->profile->name
            ]
        );
    }
}
