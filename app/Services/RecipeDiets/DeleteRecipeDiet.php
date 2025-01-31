<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use Illuminate\Support\Facades\DB;

class DeleteRecipeDiet
{
    public function delete(RecipeDiet $recipeDiet): RecipeDiet|string
    {
        try {
            DB::beginTransaction();

            if ($recipeDiet->recipes()->exists()) {
                throw new \Exception('This diet cannot be deleted because it is associated with one or more recipes');
            }

            $recipeDiet->delete();

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
