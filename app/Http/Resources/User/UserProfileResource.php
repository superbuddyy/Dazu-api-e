<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class UserProfileResource extends JsonResource
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
            'name' => $this->name ?? null,
            'phone' => $this->phone ?? null,
            'city' => $this->city ?? null,
            'default_avatar' => $this->default_avatar ?? null,
            'street' => $this->street ?? null,
            'zip_code' => $this->zip_code ?? null,
            'country' => $this->country ?? null,
            'nip' => $this->nip ?? null,
            'newsletter' => $this->newsletter ?? null,
        ];
    }
}
