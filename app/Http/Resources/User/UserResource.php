<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'role' => $this->role->name,
            'image' => new ImageResource($this->whenLoaded('image')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
