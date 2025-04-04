<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\CreateUser;
use App\Services\User\DeleteUser;
use App\Services\User\ListUser;
use App\Services\User\ShowAuthUser;
use App\Services\User\ShowUser;
use App\Services\User\ToggleFavoritePost;
use App\Services\User\ToggleFavoriteRecipe;
use App\Services\User\UpdateRole;
use App\Services\User\UpdateUser;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(ListUser $service)
    {
        $this->authorize('viewAny', User::class);

        $filters = [
            'search' => request()->input('search'),
            'role' => request()->input('role'),
            'birthday_start' => request()->input('birthday_start'),
            'birthday_end' => request()->input('birthday_end'),
            'order_by' => request()->input('order_by', 'created_at'),
            'order_direction' => request()->input('order_direction', 'desc')
        ];

        $perPage = request()->input('per_page', 10);

        $posts = $service->list($filters, $perPage);

        return response()->json($posts);
    }

    public function store(Request $request, CreateUser $service)
    {
        return $this->execute(function () use ($request, $service) {
            $data = $request->validate(User::createRules());
            $user = $service->create($data);
            $token = $user->createToken('auth_token');

            return response()->json([
                'user' => $user,
                'token' => $token->plainTextToken,
            ], 201);
        });
    }

    public function show(User $user, ShowUser $service)
    {
        return $this->execute(function () use ($user, $service) {
            $this->authorize('view', $user);
            $userData = $service->show($user->id);
            return response()->json($userData);
        });
    }

    public function update(Request $request, User $user, UpdateUser $service)
    {
        return $this->execute(function () use ($request, $user, $service) {
            $this->authorize('update', $user);
            $data = $request->validate(User::updateRules($user->id));
            $userData = $service->update($user->id, $data);
            return response()->json($userData);
        });
    }

    public function destroy(User $user, DeleteUser $service)
    {
        return $this->execute(function () use ($user, $service) {
            $this->authorize('delete', $user);
            $response = $service->delete($user->id);
            return response()->json($response);
        });
    }

    public function authUser(User $user, ShowAuthUser $service)
    {
        return $this->execute(function () use ($user, $service) {
            $this->authorize('view', $user);
            $response = $service->show();
            return response()->json($response);
        });
    }

    public function updateRole(Request $request, UpdateRole $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('viewAny', User::class);
            $data = $request->validate(User::setRoleRules());
            $userData = $service->update($data);
            return response()->json($userData);
        });
    }

    public function toggleFavoritePost(Request $request, ToggleFavoritePost $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('update', auth()->user());

            $data = $request->validate([
                'post_id' => 'required|exists:posts,id',
            ]);

            $response = $service->toggle($data);

            return response()->json($response);
        });
    }
    public function toggleFavoriteRecipe(Request $request, ToggleFavoriteRecipe $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('update', auth()->user());

            $data = $request->validate([
                'recipe_id' => 'required|exists:recipes,id',
            ]);

            $response = $service->toggle($data);

            return response()->json($response);
        });
    }
}
