<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\Cache;

class ShowRecipe
{
    public function show($id)
    {
        return Cache::remember("recipe.{$id}", now()->addHour(), function () use ($id) {
            $recipe = Recipe::with([
                'diets' => fn($q) => $q->select('recipe_diets.id', 'recipe_diets.name'),
                'category' => fn($q) => $q->select('id', 'name'),
                'steps' => fn($q) => $q->select('id', 'order', 'description', 'recipe_id'),
                'ingredients' => fn($q) => $q->select('id', 'name', 'quantity', 'unit_id', 'recipe_id'),
                'ingredients.unit' => fn($q) => $q->select('id', 'name'),
                'image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
                'user' => fn($q) => $q->select('id', 'name'),
                'user.image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
            ])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->findOrFail($id);

            $userId = auth('sanctum')->id();
            if ($userId) {
                $recipe->loadExists([
                    'favoritedByUsers as is_favorited' => function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    }
                ]);
            } else {
                $recipe->is_favorited = false;
            }

            if ($recipe->image) {
                $recipe->image->makeHidden('path');
            }

            if ($recipe->user && $recipe->user->image) {
                $recipe->user->image->makeHidden('path');
            }

            return $recipe->toArray();
        });
    }
}
