<?php

namespace App\Services\RecipeDiets;

use App\Models\RecipeDiet;
use Illuminate\Support\Facades\DB;

class CreateRecipeDiet
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeDiet = RecipeDiet::create($data);

            DB::commit();

            return $recipeDiet;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
