<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Recipe\AuthorResource;
use App\Http\Resources\Post\CategoryResource;
use App\Http\Resources\Post\TopicResource;
use App\Http\Resources\Recipe\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $averageRating = isset($this->average_rating) ? round($this->average_rating, 2) : 0;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'image' => new ImageResource($this->whenLoaded('image')),
            'average_rating' => $averageRating,
            'ratings_count' => $this->whenCounted('ratings'),
            'is_favorited' => (bool) ($this->is_favorited ?? false),
            'author' => new AuthorResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'topics' => TopicResource::collection($this->whenLoaded('topics')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
