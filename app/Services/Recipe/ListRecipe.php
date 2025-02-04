<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ListRecipe
{
    public function list(array $filters = [])
    {
        $query = Recipe::with([
            'diets',
            'category',
            'steps',
            'ingredients.unit',
            'image',
            'user' => function ($query) {
                $query->select('id', 'name'); // Only load id and name from the user table
            }
        ]);

        // Apply filters if provided
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        return $query->get();
    }
}
