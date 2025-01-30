<?php

namespace App\Services\User;

use App\Models\User;
use Arr;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUser
{
    public function update(int $id, array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            $user->update($data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
