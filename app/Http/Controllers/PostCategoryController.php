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

class PostCategoryController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListPostCategory $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $categories = $service->list($perPage);
            return response()->json($categories);
        });
    }

    public function store(Request $request, CreatePostCategory $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', PostCategory::class);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(PostCategory::createRules());

            $category = $service->create($data);
            return response()->json($category, 201);
        });
    }

    public function show(PostCategory $postCategory, ShowPostCategory $service)
    {
        return $this->execute(function () use ($postCategory, $service) {
            $category = $service->show($postCategory->id);
            return response()->json($category);
        });
    }

    public function update(Request $request, PostCategory $postCategory, UpdatePostCategory $service)
    {
        return $this->execute(function () use ($request, $postCategory, $service) {
            $this->authorize("update", $postCategory);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(PostCategory::updateRules());

            $category = $service->update($postCategory->id, $data);
            return response()->json($category);
        });
    }

    public function destroy(PostCategory $postCategory, DeletePostCategory $service)
    {
        return $this->execute(function () use ($postCategory, $service) {
            $this->authorize("delete", $postCategory);
            $response = $service->delete($postCategory->id);
            return response()->json($response);
        });
    }
}
