<?php

namespace App\Services\RecipeCategory;

use App\Models\RecipeCategory;
use Illuminate\Support\Facades\DB;

class CreateRecipeCategory
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeCategory = RecipeCategory::create($data);

            DB::commit();

            return $recipeCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
