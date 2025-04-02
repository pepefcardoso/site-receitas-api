<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ShowRecipe
{
    public function show($id)
    {
        $query = Recipe::with([
            'diets',
            'category',
            'steps',
            'ingredients.unit',
            'image',
            'user.image'
        ])
        ->withAvg('ratings', 'rating')
        ->withCount('ratings');

        if (auth()->check()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ]);
        }

        return $query->findOrFail($id);
    }
}
