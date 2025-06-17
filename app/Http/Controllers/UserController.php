<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

// O controller agora herda diretamente do Controller do Laravel.
class UserController extends Controller
{
    public function __construct()
    {
        // Middleware de autenticação para todas as rotas, exceto 'store' (registro).
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request, ListUser $service): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        // A validação dos filtros pode ser movida para um FormRequest se ficar complexa.
        $filters = $request->only(['search', 'role', 'birthday_start', 'birthday_end', 'order_by', 'order_direction']);
        $perPage = $request->input('per_page', 10);

        $users = $service->list($filters, $perPage);
        return response()->json($users);
    }

    public function store(StoreUserRequest $request, CreateUser $service): JsonResponse
    {
        // A validação é feita automaticamente pelo StoreUserRequest.
        $user = $service->create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    public function show(User $user, ShowUser $service): JsonResponse
    {
        $this->authorize('view', $user);
        $userData = $service->show($user->id);
        return response()->json($userData);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $service): JsonResponse
    {
        // A autorização e validação são feitas pelo UpdateUserRequest.
        $userData = $service->update($user, $request->validated());
        return response()->json($userData);
    }

    public function destroy(User $user, DeleteUser $service): JsonResponse
    {
        $this->authorize('delete', $user);
        $response = $service->delete($user->id);
        return response()->json($response);
    }

    public function authUser(ShowAuthUser $service): JsonResponse
    {
        // Não é necessário autorizar aqui, pois já está protegido pelo middleware.
        $response = $service->show(auth()->id());
        return response()->json($response);
    }

    public function updateRole(UpdateUserRoleRequest $request, UpdateRole $service): JsonResponse
    {
        // A autorização e validação são feitas pelo UpdateUserRoleRequest.
        $userData = $service->update($request->validated());
        return response()->json($userData);
    }

    public function toggleFavoritePost(ToggleFavoriteRequest $request, ToggleFavoritePost $service): JsonResponse
    {
        $this->authorize('update', auth()->user());
        $response = $service->toggle($request->validated());
        return response()->json($response);
    }

    public function toggleFavoriteRecipe(ToggleFavoriteRequest $request, ToggleFavoriteRecipe $service): JsonResponse
    {
        $this->authorize('update', auth()->user());
        $response = $service->toggle($request->validated());
        return response()->json($response);
    }
}