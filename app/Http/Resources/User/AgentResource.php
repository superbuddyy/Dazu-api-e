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
        return [
            'id' => $this->id,
            'name' => $this->profile->name,
            'email' => $this->email,
            'avatar' => 'https://i.pravatar.cc',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
