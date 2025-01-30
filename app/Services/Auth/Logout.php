<?php

namespace App\Services\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
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
            return $e->getMessage();
        }
    }
}
