<?php

namespace App\Services\User;

use App\Models\User;
use App\Notifications\CreatedUser;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    public function create(array $data): User|string
    {
        try {
            DB::beginTransaction();

            $userData = Arr::except($data, ['password_confirmation']);
            $user = User::create($userData);

            $user->notify(new CreatedUser($user));

            DB::commit();

            return $user->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
