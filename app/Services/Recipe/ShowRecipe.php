<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\Cache;

class ShowRecipe
{
    public function show($id)
    {
        $recipe = Cache::remember("recipe_model.{$id}", now()->addHour(), function () use ($id) {
            return Recipe::with([
                'diets',
                'category',
                'steps',
                'ingredients.unit',
                'image',
                'user.image'
            ])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->findOrFail($id);
        });

        if ($userId = auth('sanctum')->id()) {
            $recipe->loadExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
            ]);
        }

        return $recipe;
    }
}
