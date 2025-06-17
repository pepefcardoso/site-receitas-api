<?php

namespace App\Http\Resources;

use App\Http\Resources\Post\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'image' => new ImageResource($this->whenLoaded('image')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'average_rating' => round($this->whenAggregated('ratings', 'rating', 'avg') ?? 0, 2),
            'ratings_count' => $this->whenCounted('ratings'),
            'is_favorited' => (bool) ($this->is_favorited ?? false),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
