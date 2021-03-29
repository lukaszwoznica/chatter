<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'text' => $this->text,
            'sender' => new UserResource($this->sender),
            'recipient' => new UserResource($this->recipient),
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
