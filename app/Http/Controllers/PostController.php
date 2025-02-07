<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\Post\CreatePost;
use App\Services\Post\DeletePost;
use App\Services\Post\ListPost;
use App\Services\Post\ShowPost;
use App\Services\Post\UpdatePost;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class PostController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListPost $service)
    {
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
    }

    public function store(Request $request, CreatePost $service)
    {
        $this->authorize('create', Post::class);

        $data = $request->validate(Post::createRules());

        $Post = $service->create($data);

        return response()->json($Post, 201);
    }

    public function show(Post $Post, ShowPost $service)
    {
        $Post = $service->show($Post->id);

        return response()->json($Post);
    }

    public function update(Request $request, Post $Post, UpdatePost $service)
    {
        $this->authorize("update", $Post);

        $data = $request->validate(Post::updateRules());

        $Post = $service->update($Post->id, $data);

        return response()->json($Post);
    }

    public function destroy(Post $Post, DeletePost $service)
    {
        $this->authorize("delete", $Post);

        $response = $service->delete($Post);

        return response()->json($response);
    }

    public function userPosts(ListPost $service)
    {
        $filters = [
            'title' => request()->input('title'),
            'category_id' => request()->input('category_id'),
            'topics' => request()->input('topics'),
            'user_id' => request()->input('user_id'),
            'order_by' => request()->input('order_by', 'created_at'),
            'order_direction' => request()->input('order_direction', 'desc')
        ];

        $perPage = request()->input('per_page', 10);

        $posts = $service->list($filters, $perPage);

        return response()->json($posts);
    }
}
