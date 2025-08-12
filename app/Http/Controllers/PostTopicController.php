<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
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
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'post_topics';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $topics = $this->getCachedAndPaginated($request, PostTopic::query());

        return PostTopicResource::collection($topics);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $topic = PostTopic::create($request->validated());

        $this->flushResourceCache();

        return (new PostTopicResource($topic))->response()->setStatusCode(201);
    }

    public function show(PostTopic $postTopic): PostTopicResource
    {
        return new PostTopicResource($postTopic);
    }

    public function update(UpdateRequest $request, PostTopic $postTopic): PostTopicResource
    {
        $postTopic->update($request->validated());

        $this->flushResourceCache();

        return new PostTopicResource($postTopic);
    }

    public function destroy(PostTopic $postTopic): JsonResponse
    {
        $this->authorize('delete', $postTopic);

        if ($postTopic->posts()->exists()) {
            throw ValidationException::withMessages(['topic' => 'This topic is in use and cannot be deleted.']);
        }

        $postTopic->delete();

        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
