<?php

declare(strict_types=1);

namespace App\Http\Resources\PreviewOffer;

use App\Enums\AvatarType;
use App\Http\Resources\Attributes\AttributeValueResource;
use App\Http\Resources\Photo\PhotoResource;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PreviewOfferExtendedResource extends JsonResource
{
    /**
     * @var mixed|null
     */
    private $offerToken;

    public function __construct($resource, ?string $offerToken = null)
    {
        parent::__construct($resource);
        $this->offerToken = $offerToken;
    }

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
            $favorite = false;
            $isFavorite = $favorite ? true : false;
            $allowNotifications = $favorite ? $favorite->allow_notifications : false;
        }
        try {
            $videoAvatar = $this->user->videoAvatar['file']['url'] ?? null;
            $avatar = $this->avatars[0]['file']['url'] ?? url('/svg/avatar.svg');
            $default_avatar = $this->user->profile->default_avatar ?? null;
        } catch (Exception $e) {
            $videoAvatar = null;
            $avatar = null;
            $default_avatar = null;
        }
        $photos = array();
        $project_plan_photos = array();
        foreach ($this->photos as $img) {
            $obj = [
                    'id' => $img->id,
                    'position' => $img->position,
                    'url' => $img->file['url'],
                    'path_name' => $img->file['path_name'],
                ];
            if ($img->img_type == 'photo') {
                $photos[] = $obj;
            }
            if ($img->img_type == 'project_plan') {
                $project_plan_photos[] = $obj;
            }
        }
        // if ($avatar && !$videoAvatar) {
        //     $default_avatar = 'photo';
        // }
        // if ($videoAvatar && !$avatar) {
        //     $default_avatar = 'video';
        // }
        // if (!$videoAvatar && !$avatar){
        //     $avatar = url('/storage/images/avatar.svg');
        //     $default_avatar = 'photo';
        // }
        $role = null;
        $is_favorite_user = false;
        try {
            $company = $this->getCompanyData();
        } catch (Exception $e) {
            $company = [];
        }
        try {
            $user = [
                'id' => $this->user->id ?? null,
                'name' => $this->user->profile->name ?? null,
                'type' => $role,
                'avatar' => $avatar,
                'video_avatar' => $videoAvatar,
                'email' => $this->user->email ?? null,
                'default_avatar' => $default_avatar,
                'avatar_expire_time' => $this->user->avatar['expire_date'] ?? null,
                'video_avatar_expire_time' => $this->user->videoAvatar['expire_date'] ?? null,
                'is_favorite_user' => $is_favorite_user,
            ];
        } catch (Exception $e) {
            $user = [];
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
                'is_promoted' => false,
                'attributes' => $attributes->map(function ($attribute) {
                        return new AttributeValueResource($attribute);
                    }) ?? null,
                'is_favorite' => $isFavorite ?? false,
                'allow_notifications' => $allowNotifications ?? false,
                // 'subscription' => $this->activeSubscription->id ?? null,
                'user' => $user,
                'company' => $company,
                'photos' => $photos,
                'project_plan_photos' => $project_plan_photos,
                'offer_token' => $this->offerToken ?? null
            ]
        );
    }

    private function getCompanyData ()
    {
        $companyModel = $this->user->company ?? null;
        if (!$companyModel) {
            return null;
        }
        $default_avatar = $this->user->profile->default_avatar ?? null;
        if (!isset($companyModel->avatar->file['url']) && isset($companyModel->video_avatar->file['url'])) {
            $companyAvatar = null;
        } elseif (isset($companyModel->avatar->file['url'])) {
            $companyAvatar = $companyModel->avatar->file['url'];
        } else {
            $companyAvatar = url('/svg/avatar.svg');
            $default_avatar = 'photo';
        }
        $companyVideoAvatar = $companyModel->video_avatar->file['url'] ?? null;
        if ($companyAvatar && !$companyVideoAvatar) {
            $default_avatar = 'photo';   
        }
        if (!$companyAvatar && $companyVideoAvatar) {
            $default_avatar = 'video';   
        }
        if (!$companyAvatar && !$companyVideoAvatar) {
            $default_avatar = 'photo';
        }
        return [
            'name' => $companyModel->name,
            'default_avatar' => $default_avatar,
            'avatar' => $companyAvatar,
            'video_avatar' => $companyVideoAvatar,
            'avatar_expire_time' => $companyModel->avatar['expire_date'] ?? null,
            'video_avatar_expire_time' => $companyModel->videoAvatar['expire_date'] ?? null
        ];
    }
}
