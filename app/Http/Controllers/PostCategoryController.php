<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use App\Services\PostCategory\CreatePostCategory;
use App\Services\PostCategory\DeletePostCategory;
use App\Services\PostCategory\ListPostCategory;
use App\Services\PostCategory\ShowPostCategory;
use App\Services\PostCategory\UpdatePostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class PostCategoryController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListPostCategory $service)
    {
        $categories = $service->list();

        return response()->json($categories);
    }

    public function store(Request $request, CreatePostCategory $service)
    {
        $this->authorize('create', PostCategory::class);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(PostCategory::rules());

        $category = $service->create($data);

        return response()->json($category, 201);
    }

    public function show(PostCategory $PostCategory, ShowPostCategory $service)
    {
        $category = $service->show($PostCategory->id);

        return response()->json($category);
    }

    public function update(Request $request, PostCategory $PostCategory, UpdatePostCategory $service)
    {
        $this->authorize("update", $PostCategory);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(PostCategory::rules());

        $category = $service->update($PostCategory, $data);

        return response()->json($category);
    }

    public function destroy(PostCategory $postCategory, DeletePostCategory $service)
    {
        $this->authorize("delete", $postCategory);

        $response = $service->delete($postCategory->id);

        return response()->json($response);
    }

}
