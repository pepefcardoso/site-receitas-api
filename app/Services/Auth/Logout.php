<?php

namespace App\Services\Auth;

use Exception;
use Illuminate\Support\Facades\DB;

class Logout
{
    public function logout()
    {
        try {
            DB::beginTransaction();

            auth()->user()->tokens()->delete();

            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
