<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;
use Illuminate\Support\Facades\DB;

class CreateRecipeUnit
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeUnit = RecipeUnit::create($data);

            DB::commit();

            return $recipeUnit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
