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
use Illuminate\Routing\Controller as BaseController;

class PostTopicController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListPostTopic $service)
    {
        $diets = $service->list();

        return response()->json($diets);
    }

    public function store(Request $request, CreatePostTopic $service)
    {
        $this->authorize('create', PostTopic::class);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(PostTopic::rules());

        $diet = $service->create($data);

        return response()->json($diet, 201);
    }

    public function show(PostTopic $PostTopic, ShowPostTopic $service)
    {
        $diet = $service->show($PostTopic->id);

        return response()->json($diet);
    }

    public function update(Request $request, PostTopic $PostTopic, UpdatePostTopic $service)
    {
        $this->authorize("update", $PostTopic);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(PostTopic::rules());

        $diet = $service->update($PostTopic, $data);

        return response()->json($diet);
    }

    public function destroy(PostTopic $PostTopic, DeletePostTopic $service)
    {
        $this->authorize("delete", $PostTopic);

        $response = $service->delete($PostTopic);

        return response()->json($response);
    }
}
