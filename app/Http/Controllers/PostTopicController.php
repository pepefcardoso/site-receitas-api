<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostTopic\StoreRequest;
use App\Http\Requests\PostTopic\UpdateRequest;
use App\Http\Resources\PostTopicResource;
use App\Models\PostTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class PostTopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index()
    {
        return PostTopicResource::collection(PostTopic::all());
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $topic = PostTopic::create($request->validated());
        return (new PostTopicResource($topic))->response()->setStatusCode(201);
    }

    public function show(PostTopic $postTopic)
    {
        return new PostTopicResource($postTopic);
    }

    public function update(UpdateRequest $request, PostTopic $postTopic)
    {
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
