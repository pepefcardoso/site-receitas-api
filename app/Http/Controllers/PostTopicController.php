<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostTopic\StoreRequest;
use App\Http\Requests\PostTopic\UpdateRequest;
use App\Http\Resources\PostTopic\PostTopicResource;
use App\Models\PostTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class PostTopicController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'name');
        $orderDirection = $request->input('order_direction', 'asc');

        $topics = PostTopic::orderBy($orderBy, $orderDirection)->paginate($perPage);

        return PostTopicResource::collection($topics);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', PostTopic::class);
        $topic = PostTopic::create($request->validated());
        return (new PostTopicResource($topic))->response()->setStatusCode(201);
    }

    public function show(PostTopic $postTopic)
    {
        return new PostTopicResource($postTopic);
    }

    public function update(UpdateRequest $request, PostTopic $postTopic)
    {
        $this->authorize('update', $postTopic);
        $postTopic->update($request->validated());
        return new PostTopicResource($postTopic);
    }

    public function destroy(PostTopic $postTopic)
    {
        $this->authorize('delete', $postTopic);
        if ($postTopic->posts()->exists()) {
            throw ValidationException::withMessages(['topic' => 'This topic is in use and cannot be deleted.']);
        }
        $postTopic->delete();
        return response()->json(null, 204);
    }
}
