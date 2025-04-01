<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use Illuminate\Support\Facades\DB;

class UpdateRecipeCategory
{
    public function update(RecipeCategory $recipeCategory, array $data)
    {
        try {
            DB::beginTransaction();

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
