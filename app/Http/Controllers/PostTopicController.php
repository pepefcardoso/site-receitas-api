<?php

namespace App\Http\Controllers;

use App\Models\PostTopic;
use App\Services\PostTopics\CreatePostTopic;
use App\Services\PostTopics\DeletePostTopic;
use App\Services\PostTopics\ListPostTopic;
use App\Services\PostTopics\ShowPostTopic;
use App\Services\PostTopics\UpdatePostTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostTopicController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListPostTopic $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $topics = $service->list($perPage);
            return response()->json($topics);
        });
    }

    public function store(Request $request, CreatePostTopic $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', PostTopic::class);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(PostTopic::createRules());

            $topic = $service->create($data);
            return response()->json($topic, 201);
        });
    }

    public function show(PostTopic $postTopic, ShowPostTopic $service)
    {
        return $this->execute(function () use ($postTopic, $service) {
            $topic = $service->show($postTopic->id);
            return response()->json($topic);
        });
    }

    public function update(Request $request, PostTopic $postTopic, UpdatePostTopic $service)
    {
        return $this->execute(function () use ($request, $postTopic, $service) {
            $this->authorize("update", $postTopic);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(PostTopic::updateRules());

            $topic = $service->update($postTopic->id, $data);
            return response()->json($topic);
        });
    }

    public function destroy(PostTopic $postTopic, DeletePostTopic $service)
    {
        return $this->execute(function () use ($postTopic, $service) {
            $this->authorize("delete", $postTopic);
            $response = $service->delete($postTopic->id);
            return response()->json($response);
        });
    }
}
