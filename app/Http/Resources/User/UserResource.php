<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->profile->name ?? null,
            'phone' => $this->profile->phone ?? null,
            'newsletter' => $this->profile->newsletter ?? null,
            'address' => [
                'city' => $this->profile->city ?? null,
                'street' => $this->profile->street ?? null,
                'zip_code' => $this->profile->zip_code ?? null,
                'country' => $this->profile->country ?? null,
            ],
            'nip' => $this->profile->nip ?? null,
            'email' => $this->email,
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->getAllPermissions()->toArray()
            ),
            'avatar' => $this->avatar->file['url'] ?? null,
            'video_avatar' => $this->videoAvatar->file['url'] ?? null,
        ];
    }
}
