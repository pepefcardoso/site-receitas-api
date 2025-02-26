<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Auth\Login;
use App\Services\Auth\Logout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout', 'me']);
//        $this->authorizeResource(User::class, 'user');
    }

    public function login(Request $request, Login $service)
    {
        try {
            $data = $request->validate(User::loginRules());
            $result = $service->login($data);
            return response()->json(['token' => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [$e->getMessage()],
            ], 401);
        }
    }

    public function logout(Logout $service)
    {
        $service->logout();

        return response()->json(null);
    }

    public function sendResetLink(Request $request)
    {
        Log::info('Reset password request received', ['email' => $request->email]);

        $request->validate(['email' => 'required|email']);

        Log::info('Attempting to send reset link to: ' . $request->email);

        $status = Password::sendResetLink($request->only('email'));

        Log::info('Password reset status: ' . $status);

        return response()->json(['message' => __($status)], $status === Password::RESET_LINK_SENT ? 200 : 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }
}
