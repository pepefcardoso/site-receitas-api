<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use Illuminate\Support\Facades\DB;

class DeleteRecipeCategory
{
    public function delete(RecipeCategory $recipeCategory)
    {
        try {
            DB::beginTransaction();

            $recipeCategory->delete();

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
