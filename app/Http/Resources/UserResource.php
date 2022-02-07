<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'is_online' => $this->is_online,
            'last_online_at' => $this->last_online_at?->format('Y-m-d H:i:s'),
            'avatar_url' => $this->getFirstMediaUrl('avatar'),
            'avatar_thumb_url' => $this->getFirstMediaUrl('avatar', 'thumb')
        ];
    }
}
