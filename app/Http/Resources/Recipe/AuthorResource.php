<?php

namespace App\Http\Resources\Recipe;

use App\Http\Resources\Recipe\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => new ImageResource($this->whenLoaded('image')),
        ];
    }
}
