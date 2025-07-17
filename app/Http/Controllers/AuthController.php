<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\User\AuthUserResource;
use App\Services\Auth\Login;
use App\Services\Auth\Logout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class AuthController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('logout');
    }

    public function login(LoginRequest $request, Login $service)
    {
        $token = $service->login($request->validated());
        $user = $service->getUser();

        return new AuthUserResource($user->load('image'), $token);
    }

    public function logout(Logout $service): JsonResponse
    {
        $service->logout();
        return response()->json(null, 204);
    }

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink($request->validated());

        return response()->json([
            'message' => 'Se o e-mail fornecido estiver em nossa base de dados, um link para redefinição de senha será enviado.'
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->validated(),
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
