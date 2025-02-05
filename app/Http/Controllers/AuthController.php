<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Auth\Login;
use App\Services\Auth\Logout;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['login']);
        $this->authorizeResource(User::class, 'user');
    }

    public function login(Request $request, Login $service)
    {
        try {
            $data = $request->validate(User::loginRules());
            $result = $service->login($data);
            return response()->json($result);
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
}
