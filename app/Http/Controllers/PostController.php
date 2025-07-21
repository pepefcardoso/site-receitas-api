<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\FilterPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCollectionResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use App\Services\Post\CreatePost;
use App\Services\Post\DeletePost;
use App\Services\Post\ListFavoritePosts;
use App\Services\Post\ListPost;
use App\Services\Post\ListUserPosts;
use App\Services\Post\ShowPost;
use App\Services\Post\UpdatePost;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(FilterPostRequest $request, ListPost $service): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 10;

        $posts = $service->list($filters, $perPage);

        return PostCollectionResource::collection($posts);
    }

    public function store(StorePostRequest $request, CreatePost $service): PostResource
    {
        $post = $service->create($request->validated());
        $post->load(['user.image', 'category', 'topics', 'image']);
        return new PostResource($post);
    }

    public function show(Post $post, ShowPost $service): PostResource
    {
        $detailedPost = $service->show($post);
        return new PostResource($detailedPost);
    }

    public function update(UpdatePostRequest $request, Post $post, UpdatePost $service): PostResource
    {
        $updatedPost = $service->update($post, $request->validated());
        return new PostResource($updatedPost);
    }

    public function destroy(Post $post, DeletePost $service)
    {
        $this->authorize("delete", $post);
        $service->delete($post);
        return response()->json(null, 204);
    }

    public function userPosts(ListUserPosts $service): AnonymousResourceCollection
    {
        $perPage = request()->input('per_page', 10);
        $userPosts = $service->list($perPage);
        return PostCollectionResource::collection($userPosts);
    }

    public function favorites(ListFavoritePosts $service): AnonymousResourceCollection
    {
        $perPage = request()->input('per_page', 10);
        $favorites = $service->list($perPage);
        return PostCollectionResource::collection($favorites);
    }
}
