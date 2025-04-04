<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\DB;

class DeleteRecipeIngredient
{
    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $ingredient = RecipeIngredient::findOrFail($id);
            $ingredient->delete();

            DB::commit();

            return $id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
