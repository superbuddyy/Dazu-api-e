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
        $videoAvatar = $this->videoAvatar->file['url'] ?? null;
        $avatar = $this->avatar->file['url'] ?? null;
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
        $role = null;
        try {
            if ($this->id) {
                $role = $this->getRoleName() ?? null;
            }
        } catch (Exception $e) {
            $role = null;
        }
        try {
            if ($this->id) {
                $exist = $this->getFavoriteUser($this->id);
                $is_favorite_user = $exist ? true : false;
            }
        } catch (Exception $e) {
            $is_favorite_user = false;
        }
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
            'type' => $role,
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->getAllPermissions()->toArray()
            ),
            'avatar' => $avatar,
            'video_avatar' => $videoAvatar,
            'default_avatar' => $default_avatar,
            'avatar_expire_time' => $this->avatar['expire_date'] ?? null,
            'video_avatar_expire_time' => $this->videoAvatar['expire_date'] ?? null,
            'is_favorite_user' => $is_favorite_user,
        ];
    }
}
