<?php

namespace App\Services\Auth;

use Exception;

class Logout
{
    public function logout(): bool
    {
        try {
            auth()->user()->currentAccessToken()->delete();

            return true;
        } catch (Exception $e) {
            report($e);
            throw $e;
        }
    }
}
