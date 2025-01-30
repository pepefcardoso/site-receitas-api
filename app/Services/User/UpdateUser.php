<?php

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUser
{
    public function update(int $id, array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            $userData = Arr::only($data, ['name', 'email', 'password']);
            $user->update($userData);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
