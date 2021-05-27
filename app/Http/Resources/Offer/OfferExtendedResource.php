<?php

declare(strict_types=1);

namespace App\Http\Resources\Offer;

use App\Enums\AvatarType;
use App\Http\Resources\Attributes\AttributeValueResource;
use App\Http\Resources\Photo\PhotoResource;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OfferExtendedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request|FormRequest $request
     * @return array
     */
    public function toArray($request): array
    {
        $attributes = $this->attributes;
        if (Auth::user()) {
            $favorite = $this->getFavorite((string)Auth::id());
            $isFavorite = $favorite ? true : false;
            $allowNotifications = $favorite ? $favorite->allow_notifications : false;
        }

        $videoAvatar = $this->user->videoAvatar['file']['url'] ?? null;
        $avatar = $this->user->avatar['file']['url'] ?? null;

        if (!$videoAvatar && !$avatar){
            $avatar = url('/storage/images/avatar.svg');
        }

        $company = $this->getCompanyData();

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
                'status' => $this->status ?? null,
                'media' => [ // TODO: Remove old links
                    'videos' => [
                        $this->links['video'] ?? '',
                        $this->links['video_2'] ?? ''
                    ],
                    'virtual_walks' => [
                        $this->links['walk_video'] ?? ''
                    ],
                ],
                'location' => [
                    'lat' => $this->lat ?? null,
                    'lon' => $this->lon ?? null,
                    'name' => $this->location_name ?? null,
                ],
                'created_at' => $this->created_at->format('Y-m-d H:II') ?? null,
                'updated_at' => $this->updated_at->format('Y-m-d H:II') ?? null,
                'type' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 1;
                })->first()->result ?? '',
                'is_installments' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 2 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'is_per_month' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 5 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'is_for_negotiations' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 6 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'is_with_bills' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 7 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'is_free' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 8 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'is_available_now' => $attributes->filter(function ($attribute) {
                    return $attribute->id === 13 && $attribute->pivot->value === 'true';
                })->isNotEmpty(),
                'attributes' => $attributes->map(function ($attribute) {
                        return new AttributeValueResource($attribute);
                    }) ?? null,
                'is_favorite' => $isFavorite ?? false,
                'allow_notifications' => $allowNotifications ?? false,
                'subscription' => $this->activeSubscription->id ?? null,
                'user' => [
                    'id' => $this->user->id ?? null,
                    'name' => $this->user->profile->name ?? null,
                    'type' => $this->user->getRoleName() ?? null,
                    'avatar' => $avatar,
                    'video_avatar' => $videoAvatar,
                ],
                'company' => $company ?? null,
                'photos' => $this->photos->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'position' => $img->position,
                        'url' => $img->file['url'],
                        'path_name' => $img->file['path_name'],
                    ];
                })
            ]
        );
    }

    private function getCompanyData ()
    {
        $companyModel = $this->user->company;
        if (!isset($companyModel)) {
            return [];
        }

        if (!isset($companyModel->avatar->file['url']) && isset($companyModel->video_avatar->file['url'])) {
            $companyAvatar = null;
        } elseif (isset($companyModel->avatar->file['url'])) {
            $companyAvatar = $companyModel->avatar->file['url'];
        } else {
            $companyAvatar = url('/svg/avatar.svg');
        }

        return [
            'name' => $companyModel->name,
            'avatar' => $companyAvatar,
            'video_avatar' => $companyModel->video_avatar->file['url'] ?? null
        ];
    }
}
