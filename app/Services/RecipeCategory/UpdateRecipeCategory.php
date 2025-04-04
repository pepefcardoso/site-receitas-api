<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use Illuminate\Support\Facades\DB;

class UpdateRecipeCategory
{
    public function update(int $recipeCategoryId, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeCategory = RecipeCategory::findOrFail($recipeCategoryId);
            $recipeCategory->fill($data);
            $recipeCategory->save();

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
