<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use Illuminate\Support\Facades\DB;

class DeleteRecipeDiet
{
    public function delete(int $recipeDietId): RecipeDiet|string
    {
        try {
            DB::beginTransaction();

            $recipeDiet = RecipeDiet::findOrFail($recipeDietId);

            if ($recipeDiet->recipes()->exists()) {
                throw new \Exception('This diet cannot be deleted because it is associated with one or more recipes');
            }

            $recipeDiet->delete();

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
