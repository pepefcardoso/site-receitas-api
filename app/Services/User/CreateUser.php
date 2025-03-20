<?php

namespace App\Services\User;

use App\Models\User;
use App\Notifications\CreatedUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CreateUser
{
    public function create(array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::create($data);

            Notification::route('mail', $user->email)
                ->notify(new CreatedUser($user));

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }
}
