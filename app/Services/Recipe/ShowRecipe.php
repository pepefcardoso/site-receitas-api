<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\Cache;

class ShowRecipe
{
    public function show($id)
    {
        return Cache::remember("recipe.{$id}", now()->addHour(), function () use ($id) {
            $query = Recipe::with([
                    'diets' => fn($q) => $q->select('id', 'name', 'created_at', 'updated_at'),
                    'category' => fn($q) => $q->select('id', 'name', 'created_at', 'updated_at'),
                    'steps' => fn($q) => $q->select('id', 'order', 'description', 'recipe_id', 'created_at', 'updated_at'),
                    'ingredients' => fn($q) => $q->select('id', 'name', 'quantity', 'unit_id', 'recipe_id', 'created_at', 'updated_at'),
                    'ingredients.unit' => fn($q) => $q->select('id', 'name', 'created_at', 'updated_at'),
                    'image' => fn($q) => $q
                        ->select('id', 'path', 'imageable_id', 'imageable_type', 'created_at', 'updated_at')
                        ->makeHidden('path'),
                    'user' => fn($q) => $q->select('id', 'name', 'created_at', 'updated_at'),
                    'user.image' => fn($q) => $q
                        ->select('id', 'path', 'imageable_id', 'imageable_type', 'created_at', 'updated_at')
                        ->makeHidden('path'),
                ])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings');

            if (auth()->check()) {
                $query->withExists([
                    'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', auth()->id()),
                ]);
            }

            return $query->findOrFail($id);
        });
    }
}
