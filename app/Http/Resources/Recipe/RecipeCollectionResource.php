<?php

namespace App\Http\Resources;

use App\Http\Resources\Recipe\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => new ImageResource($this->whenLoaded('image')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'diets' => DietResource::collection($this->whenLoaded('diets')),
            'average_rating' => round($this->whenAggregated('ratings', 'rating', 'avg') ?? 0, 2),
            'ratings_count' => $this->whenCounted('ratings'),
            'is_favorited' => (bool) ($this->is_favorited ?? false),
        ];
    }
}
