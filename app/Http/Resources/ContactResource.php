<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => "$this->first_name $this->last_name",
            'email' => $this->email,
            'unread_messages' => $this->unread_messages,
            'last_message' => $this->last_message,
            'is_online' => $this->is_online,
            'last_online_at' => optional($this->last_online_at)->format('Y-m-d H:i:s'),
        ];
    }
}
