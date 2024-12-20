<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['store'])
        ];
    }

    public function index()
    {
        Gate::authorize('isInternalUser');

        $users = User::all();
        return response()->json($users, 201);
    }

    public function store(Request $request)
    {
        $fields = $request->validate(User::$createRules);

        $user = User::create($fields);

        $token = $user->createToken('auth_token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    public function show(User $user)
    {
        Gate::authorize('showAndModify', $user);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('showAndModify', $user);

        $fields = $request->validate(user::$updateRules);

        $user->update($fields);

        return response()->json($user, 201);
    }

    public function destroy(User $user)
    {
        Gate::authorize('showAndModify', $user);

        $user->delete();

        return response()->json(null, 204);
    }
}
