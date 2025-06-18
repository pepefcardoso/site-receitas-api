<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCategory\StoreRequest;
use App\Http\Requests\PostCategory\UpdateRequest;
use App\Http\Resources\PostCategoryResource;
use App\Models\PostCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class PostCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    public function index()
    {
        return PostCategoryResource::collection(PostCategory::all());
    }
    public function store(StoreRequest $request): JsonResponse
    {
        $category = PostCategory::create($request->validated());
        return (new PostCategoryResource($category))->response()->setStatusCode(201);
    }
    public function show(PostCategory $postCategory)
    {
        return new PostCategoryResource($postCategory);
    }
    public function update(UpdateRequest $request, PostCategory $postCategory)
    {
        $postCategory->update($request->validated());
        return new PostCategoryResource($postCategory);
    }
    public function destroy(PostCategory $postCategory)
    {
        $this->authorize('delete', $postCategory);
        if ($postCategory->posts()->exists()) {
            throw ValidationException::withMessages(['category' => 'This category is in use and cannot be deleted.']);
        }
        $postCategory->delete();
        return response()->json(null, 204);
    }
}
