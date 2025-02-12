<?php

namespace App\Services\User;

use App\Models\User;
use DB;
use Illuminate\Support\Str;
use App\Notifications\PasswordReset;
use Log;

class ResetPassword
{
    public function reset(string $email)
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $email)->firstOrFail();

            $newPassword = Str::password(12);

            $user->password = $newPassword;
            $user->save();

            $user->notify(new PasswordReset($user->name, $newPassword));

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Password reset failed: ' . $e->getMessage());
            throw $e;
        }

    }
}
