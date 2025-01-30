<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateRecipeIngredient
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipe = Recipe::findOrFail($data['recipe_id']);
            if ($recipe->user_id !== Auth::id()) {
                throw new \Exception('You do not have permission to add ingredients to this recipe.');
            }

            $recipeIngredient = RecipeIngredient::create($data);

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
