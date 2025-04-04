<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\DB;

class UpdateRecipeIngredient
{
    public function update(int $recipeIngredientId, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeIngredient = RecipeIngredient::findOrFail($recipeIngredientId);
            $recipeIngredient->fill($data);
            $recipeIngredient->save();

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
