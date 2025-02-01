<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\CreateUser;
use App\Services\User\DeleteUser;
use App\Services\User\ListUser;
use App\Services\User\ShowUser;
use App\Services\User\UpdateUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['create']);
    }

    public function index(ListUser $service)
    {
        $this->authorize('viewAny', User::class);
        $users = $service->list();

        return response()->json($users);
    }

    public function store(Request $request, CreateUser $service)
    {
        $data = $request->validate(User::createRules());

        $user = $service->create($data);

        $token = $user->createToken('auth_token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    public function show(User $user, ShowUser $service)
    {
        $this->authorize('view', $user);

        $user = $service->show($user->id);

        return response()->json($user);
    }

    public function update(Request $request, User $user, UpdateUser $service)
    {
        $this->authorize('update', $user);

        $data = $request->validate(User::updateRules());

        $user = $service->update($user->id, $data);

        return response()->json($user);
    }

    public function destroy(User $user, DeleteUser $service)
    {
        $this->authorize('delete', $user);

        $response = $service->delete($user);

        return response()->json($response);
    }
}
