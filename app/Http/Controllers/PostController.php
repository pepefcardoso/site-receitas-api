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

    public function index(ListPost $service)
    {
        $Posts = $service->list();

        return response()->json($Posts);
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

        $service->delete($Post);

        return response()->json(null, 204);
    }
}
