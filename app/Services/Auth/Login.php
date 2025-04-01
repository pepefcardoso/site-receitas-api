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
            if (!Hash::check($password, $user->password)) {
                throw new Exception("Credenciais inválidas");
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return $token;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
