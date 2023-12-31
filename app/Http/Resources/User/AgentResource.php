<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
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
            'id' => $this->id,
            'name' => $this->profile->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'status' => $this->email_verified_at === null ? 'Nieaktywny' : 'Aktywny',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'is_favorite_user' => $is_favorite_user,
        ];
    }
}
