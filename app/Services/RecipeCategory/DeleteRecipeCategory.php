<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use Illuminate\Support\Facades\DB;

class DeleteRecipeCategory
{
    public function delete(int $recipeCategoryId)
    {
        try {
            DB::beginTransaction();

            $recipeCategory = RecipeCategory::findOrFail($recipeCategoryId);

            if ($recipeCategory->recipes()->exists()) {
                throw new \Exception('This category cannot be deleted because it is associated with one or more recipes');
            }

            $recipeCategory->delete();

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
