<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use Illuminate\Support\Facades\DB;

class UpdateRecipeDiet
{
    public function update(RecipeDiet $recipeDiet, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeDiet->fill($data);
            $recipeDiet->save();

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
