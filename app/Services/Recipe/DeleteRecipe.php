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

            if ($recipe->user_id !== auth()->id()) {
                throw new Exception("Unauthorized: You don't own this recipe.");
            }

            $recipe->delete();
            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
