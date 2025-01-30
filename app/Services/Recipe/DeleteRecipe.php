<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteRecipe
{
    public function delete(Recipe $recipe): Recipe|string
    {
        try {
            DB::beginTransaction();

            $recipe->delete();
            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
