<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\FilterUsersRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\ToggleFavoriteRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdateUserRoleRequest;
use App\Http\Resources\User\AuthUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\CreateUser;
use App\Services\User\DeleteUser;
use App\Services\User\ListUser;
use App\Services\User\UpdateUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(FilterUsersRequest $request, ListUser $service): AnonymousResourceCollection
    {
        $users = $service->list($request->validated());
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request, CreateUser $service): AuthUserResource
    {
        $user = $service->create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return new AuthUserResource($user->load('image'), $token);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user->load('image'));
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $service): UserResource
    {
        $updatedUser = $service->update($user, $request->validated());

        return new UserResource($updatedUser);
    }

    public function destroy(User $user, DeleteUser $service): JsonResponse
    {
        $this->authorize('delete', $user);

        $service->delete($user);

        return response()->json(null, 204);
    }

    public function authUser(Request $request): UserResource
    {
        $this->authorize('view', $request->user());

        return new UserResource($request->user()->load('image'));
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): UserResource
    {
        $user->update(['role' => $request->validated('role')]);

        return new UserResource($user);
    }

    public function toggleFavoritePost(ToggleFavoriteRequest $request): JsonResponse
    {
        $user = Auth::user();
        $postId = $request->validated()['post_id'] ?? null;
        $result = $user->favoritePosts()->toggle($postId);
        return response()->json($result);
    }

    public function toggleFavoriteRecipe(ToggleFavoriteRequest $request): JsonResponse
    {
        $user = Auth::user();
        $recipeId = $request->validated()['recipe_id'] ?? null;
        $result = $user->favoriteRecipes()->toggle($recipeId);
        return response()->json($result);
    }
}
