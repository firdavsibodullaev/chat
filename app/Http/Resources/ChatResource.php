<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'chat_id' => $this['id'],
            'host' => UserResource::make($this['host']),
            'guest' => UserResource::make($this['guest']),
            'messages' => MessageResource::collection($this['messages'])
        ];
    }
}
