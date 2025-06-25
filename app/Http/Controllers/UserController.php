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
        $updatedUser = $service->update($user->id, $request->validated());
        return new UserResource($updatedUser->load('image'));
    }

    public function destroy(User $user, DeleteUser $service): JsonResponse
    {
        $this->authorize('delete', $user);
        $service->delete($user->id);
        return response()->json(null, 204);
    }

    public function authUser(Request $request): UserResource
    {
        return new UserResource($request->user()->load('image'));
    }

    public function updateRole(UpdateUserRoleRequest $request): UserResource
    {
        $validated = $request->validated();
        $user = User::findOrFail($validated['user_id']);
        $user->update(['role' => $validated['role']]);
        return new UserResource($user);
    }

    public function toggleFavoritePost(ToggleFavoriteRequest $request): JsonResponse
    {
        $request->user()->favoritePosts()->toggle($request->validated('post_id'));
        return response()->json(['message' => 'Favorite status toggled successfully.']);
    }

    public function toggleFavoriteRecipe(ToggleFavoriteRequest $request): JsonResponse
    {
        $request->user()->favoriteRecipes()->toggle($request->validated('recipe_id'));
        return response()->json(['message' => 'Favorite status toggled successfully.']);
    }
}
