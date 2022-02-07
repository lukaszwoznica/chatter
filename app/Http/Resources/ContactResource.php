<?php

namespace App\Http\Resources;


class ContactResource extends UserResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
                'unread_messages' => $this->unread_messages,
                'last_message' => $this->last_message,
            ];
    }
}
