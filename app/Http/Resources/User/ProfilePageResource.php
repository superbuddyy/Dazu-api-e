<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Enums\OfferStatus;
use App\Http\Resources\Offer\OfferResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $videoAvatar = $this->videoAvatar['file']['url'] ?? null;
        $avatar = $this->avatar['file']['url'] ?? null;
        $default_avatar = $this->profile->default_avatar ?? null;
        if ($avatar && !$videoAvatar) {
            $default_avatar = 'photo';
        }
        if ($videoAvatar && !$avatar) {
            $default_avatar = 'video';
        }
        if (!$videoAvatar && !$avatar){
            $avatar = url('/storage/images/avatar.svg');
            $default_avatar = 'photo';
        }
        $is_favorite_user = false;
        try {
            if ($this->id) {
                $exist = $this->getFavoriteUser($this->id);
                $is_favorite_user = $exist ? true : false;
            }
        } catch (Exception $e) {
            $is_favorite_user = false;
        }
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->profile->name ?? null,
                'type' => $this->getRoleName() ?? null,
                'avatar' => $avatar,
                'video_avatar' => $videoAvatar,
                'default_avatar' => $default_avatar,
                'avatar_expire_time' => $this->avatar['expire_date'] ?? null,
                'video_avatar_expire_time' => $this->videoAvatar['expire_date'] ?? null,
                'created_at' => $this->created_at->format('Y-m-d') ?? null,
                'company' => $this->getCompanyData(),
                'address' => [
                    'city' => $this->profile->city ?? null,
                    'street' => $this->profile->street ?? null,
                    'zip_code' => $this->profile->zip_code ?? null,
                    'country' => $this->profile->country ?? null,
                ],
                'is_favorite_user' => $is_favorite_user,
                'offers_count' => $this->offers->where('status', OfferStatus::ACTIVE)->count()
            ],
            'offers' => $this->offers->where('status', OfferStatus::ACTIVE)->map(function ($offer) {
                return new OfferResource($offer);
            })
        ];
    }

    private function getCompanyData ()
    {
        $companyModel = $this->company;
        if (!isset($companyModel)) {
            return null;
        }
        $default_avatar = $this->profile->default_avatar ?? null;
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
            'avatar' => $companyAvatar,
            'default_avatar' => $default_avatar,
            'video_avatar' => $companyVideoAvatar,
            'avatar_expire_time' => $companyModel->avatar['expire_date'] ?? null,
            'video_avatar_expire_time' => $companyModel->videoAvatar['expire_date'] ?? null
        ];
    }
}
