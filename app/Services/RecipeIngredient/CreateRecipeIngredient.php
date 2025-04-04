<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use App\Models\Recipe;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateRecipeIngredient
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeId = data_get($data, 'recipe_id');
            if (!$recipeId) {
                throw new Exception('Recipe ID is required');
            }
            Recipe::findOrFail($recipeId);

            $recipeIngredient = RecipeIngredient::create($data);

            DB::commit();

            return $recipeIngredient;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
