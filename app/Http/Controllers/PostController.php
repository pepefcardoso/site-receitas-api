<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\Post\CreatePost;
use App\Services\Post\DeletePost;
use App\Services\Post\ListFavoritePosts;
use App\Services\Post\ListPost;
use App\Services\Post\ListUserPosts;
use App\Services\Post\ShowPost;
use App\Services\Post\UpdatePost;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PostController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListPost $service)
    {
        return $this->execute(function () use ($request, $service) {
            $validatedFilters = $request->validate(Post::filtersRules());

            $filters = [
                'search' => request()->input('search'),
                'category_id' => $validatedFilters['category_id'] ?? null,
                'order_by' => $validatedFilters['order_by'] ?? 'created_at',
                'order_direction' => $validatedFilters['order_direction'] ?? 'desc',
                'user_id' => $validatedFilters['user_id'] ?? null,
            ];

            $perPage = $validatedFilters['per_page'] ?? 10;

            $posts = $service->list($filters, $perPage);

            return response()->json($posts);
        });
    }

    public function store(Request $request, CreatePost $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', Post::class);
            $data = $request->validate(Post::createRules());
            $post = $service->create($data);
            return response()->json($post, 201);
        });
    }

    public function show(Post $post, ShowPost $service)
    {
        return $this->execute(function () use ($post, $service) {
            $post = $service->show($post->id);
            return response()->json($post);
        });
    }

    public function update(Request $request, Post $post, UpdatePost $service)
    {
        return $this->execute(function () use ($request, $post, $service) {
            $this->authorize("update", $post);
            $data = $request->validate(Post::updateRules());
            $post = $service->update($post->id, $data);
            return response()->json($post);
        });
    }

    public function destroy(Post $post, DeletePost $service)
    {
        return $this->execute(function () use ($post, $service) {
            $this->authorize("delete", $post);
            $response = $service->delete($post->id);
            return response()->json($response);
        });
    }

    public function userPosts(ListUserPosts $service)
    {
        return $this->execute(function () use ($service) {
            $perPage = request()->input('per_page', 10);
            $userPosts = $service->list($perPage);

            return response()->json($userPosts);
        });
    }

    public function favorites(ListFavoritePosts $service)
    {
        return $this->execute(function () use ($service) {
            $perPage = request()->input('per_page', 10);
            $favorites = $service->list($perPage);

            return response()->json($favorites);
        });
    }
}
