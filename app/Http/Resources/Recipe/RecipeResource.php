<?php

namespace App\Http\Resources\Recipe;

// ... outras importações
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $averageRating = $this->whenAggregated('ratings', 'rating', 'avg');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'time' => $this->time,
            'portion' => $this->portion,
            'difficulty' => $this->difficulty,
            'image' => new ImageResource($this->whenLoaded('image')),
            'average_rating' => is_numeric($averageRating) ? round($averageRating, 2) : 0,
            'ratings_count' => $this->whenCounted('ratings'),
            'is_favorited' => (bool) ($this->is_favorited ?? false),
            'author' => new AuthorResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'diets' => DietResource::collection($this->whenLoaded('diets')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'steps' => StepResource::collection($this->whenLoaded('steps')),
        ];
    }
}
