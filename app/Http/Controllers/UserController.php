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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class UserController extends Controller
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
    public function store(StoreUserRequest $request, CreateUser $service)
    {
        $user = $service->create($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        // return (new AuthUserResource($user))->withToken($token);

        $userArray = (new UserResource($user->load('image')))->resolve();
        return response()->json([
            'token' => $token,
            'user' => $userArray,
        ], 201);
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
    public function authUser(): UserResource
    {
        return new UserResource(auth()->user()->load('image'));
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
        auth()->user()->favoritePosts()->toggle($request->validated('post_id'));
        return response()->json(['message' => 'Favorite status toggled successfully.']);
    }
    public function toggleFavoriteRecipe(ToggleFavoriteRequest $request): JsonResponse
    {
        auth()->user()->favoriteRecipes()->toggle($request->validated('recipe_id'));
        return response()->json(['message' => 'Favorite status toggled successfully.']);
    }
}
