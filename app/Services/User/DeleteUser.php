<?php

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteUser
{
    public function delete(User $user): User|string
    {
        try {
            DB::beginTransaction();

            $user->delete();
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
