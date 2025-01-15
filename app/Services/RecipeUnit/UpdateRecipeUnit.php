<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;
use Illuminate\Support\Facades\DB;

class UpdateRecipeUnit
{
    public function update(RecipeUnit $recipeUnit, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeUnit->fill($data);
            $recipeUnit->save();

            DB::commit();

            return $recipeUnit;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
