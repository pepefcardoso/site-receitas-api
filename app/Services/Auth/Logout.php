<?php

namespace App\Services\Auth;

use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class Logout
{
    public function logout(): bool
    {
        try {
            /** @var \App\Models\User&\Laravel\Sanctum\HasApiTokens|null $user */
            $user = Auth::user();

            if (! $user) {
                return false;
            }

            /** @var PersonalAccessToken|null $token */
            $token = $user->currentAccessToken();

            if ($token) {
                $token->delete();
            }

            return true;
        } catch (Exception $e) {
            report($e);
            throw $e;
        }
    }
}
