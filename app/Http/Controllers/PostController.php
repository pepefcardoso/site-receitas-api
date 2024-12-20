<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        $post = Post::with(['user', 'categories'])->get();
        return response()->json($post, 201);
    }

    public function store(Request $request)
    {
        $fields = $request->validate(Post::$rules);

        $post = $request->user()->posts()->create($fields);

        $post->categories()->attach($request->categories);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        return response()->json($post->load('user', 'categories'), 201);
    }

    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);

        $fields = $request->validate(Post::$rules);

        $post->update($fields);
        $post->categories()->sync($request->categories);

        return response()->json($post, 201);
    }

    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
