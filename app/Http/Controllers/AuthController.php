<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Auth\Login;
use App\Services\Auth\Logout;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout', 'me']);
    }

    public function login(Request $request, Login $service)
    {
        return $this->execute(function () use ($request, $service) {
            $data = $request->validate(User::loginRules());
            $token = $service->login($data);
            return response()->json(['token' => $token]);
        });
    }

    public function logout(Logout $service)
    {
        return $this->execute(function () use ($service) {
            $service->logout();
            return response()->json(null, 204);
        });
    }

    public function sendResetLink(Request $request)
    {
        return $this->execute(function () use ($request) {
            $request->validate(['email' => 'required|email']);
            $status = Password::sendResetLink($request->only('email'));

            return response()->json(
                ['message' => __($status)],
                $status === Password::RESET_LINK_SENT ? 200 : 400
            );
        });
    }

    public function resetPassword(Request $request)
    {
        return $this->execute(function () use ($request) {
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
        });
    }
}
