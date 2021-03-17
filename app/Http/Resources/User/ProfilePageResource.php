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
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->profile->name ?? null,
                'created_at' => $this->created_at->format('Y-m-d') ?? null,
                'company' => [
                    'avatar' => $this->company->file ?? null
                ],
                'address' => [
                    'city' => $this->profile->city ?? null,
                    'street' => $this->profile->street ?? null,
                    'zip_code' => $this->profile->zip_code ?? null,
                    'country' => $this->profile->country ?? null,
                ],
                'offers_count' => $this->offers->count()
            ],
            'offers' => $this->offers->where('status', OfferStatus::ACTIVE)->map(function ($offer) {
                return new OfferResource($offer);
            })
        ];
    }
}
