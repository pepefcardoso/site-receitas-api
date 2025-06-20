<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Recipe\ImageResource;
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
            'role' => $this->role?->name,
            'image' => new ImageResource($this->whenLoaded('image')),
            'phone' => $this->when(auth()->check() && auth()->user()->can('view', $this->resource), $this->phone),
            'birthday' => $this->when(auth()->check() && auth()->user()->can('view', 'App\Models\User', $this->resource), $this->birthday),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
