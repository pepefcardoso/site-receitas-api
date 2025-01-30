<?php

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    public function create(array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::create($data);

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }
}
