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
        $videoAvatar = $this->user->videoAvatar['file']['url'] ?? null;
        $avatar = $this->user->avatar['file']['url'] ?? null;
        $default_avatar = $this->profile->default_avatar ?? null;
        if (!$videoAvatar && !$avatar){
            $avatar = url('/storage/images/avatar.svg');
            $default_avatar = 'photo';
        }

        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->profile->name ?? null,
                'type' => $this->getRoleName() ?? null,
                'avatar' => $avatar,
                'video_avatar' => $videoAvatar,
                'default_avatar' => $default_avatar,
                'created_at' => $this->created_at->format('Y-m-d') ?? null,
                'company' => $this->getCompanyData(),
                'address' => [
                    'city' => $this->profile->city ?? null,
                    'street' => $this->profile->street ?? null,
                    'zip_code' => $this->profile->zip_code ?? null,
                    'country' => $this->profile->country ?? null,
                ],
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

        return [
            'name' => $companyModel->name,
            'avatar' => $companyAvatar,
            'default_avatar' => $default_avatar,
            'video_avatar' => $companyModel->video_avatar->file['url'] ?? null
        ];
    }
}
