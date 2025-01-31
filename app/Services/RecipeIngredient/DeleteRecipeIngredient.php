<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\DB;

class DeleteRecipeIngredient
{
    public function delete(RecipeIngredient $recipeIngredient)
    {
        try {
            DB::beginTransaction();

            if ($recipeIngredient->recipes()->exists()) {
                throw new \Exception('This ingredient cannot be deleted because it is associated with one or more recipes');
            }

            $recipeIngredient->delete();

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
