<?php

namespace App\Services\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Login
{
    public function login(array $data)
    {
        try {
            DB::beginTransaction();

            $email = data_get($data, "email");
            $user = User::where('email', $email)->firstOrFail();

            $password = data_get($data, 'password');
            throw_if(!Hash::check($password, $user->password), Exception::class, "Credentials are incorrect");

            $token = $user->createToken('auth_token');

            DB::commit();

            return [
                'user' => $user,
                'token' => $token->plainTextToken,
            ];
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
