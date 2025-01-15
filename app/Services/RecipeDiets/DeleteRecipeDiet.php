<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use Illuminate\Support\Facades\DB;

class DeleteRecipeDiet
{
    public function delete(RecipeDiet $recipeDiet)
    {
        try {
            DB::beginTransaction();

            $recipeDiet->delete();

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
