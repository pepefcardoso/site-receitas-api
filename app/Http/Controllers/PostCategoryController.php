<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\PostCategory\StoreRequest;
use App\Http\Requests\PostCategory\UpdateRequest;
use App\Http\Resources\PostCategory\PostCategoryResource;
use App\Models\PostCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class PostCategoryController extends BaseController
{
    use ManagesResourceCaching;
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'post_categories';
    }


    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = $this->getCachedAndPaginated($request, PostCategory::query());

        return PostCategoryResource::collection($categories);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', PostCategory::class);
        $category = PostCategory::create($request->validated());
        $this->flushResourceCache();

        return (new PostCategoryResource($category))->response()->setStatusCode(201);
    }

    public function show(PostCategory $postCategory): PostCategoryResource
    {
        return new PostCategoryResource($postCategory);
    }

    public function update(UpdateRequest $request, PostCategory $postCategory): PostCategoryResource
    {
        $this->authorize('update', $postCategory);
        $postCategory->update($request->validated());
        $this->flushResourceCache();

        return new PostCategoryResource($postCategory);
    }

    public function destroy(PostCategory $postCategory): JsonResponse
    {
        $this->authorize('delete', $postCategory);
        if ($postCategory->posts()->exists()) {
            throw ValidationException::withMessages(['category' => 'This category is in use and cannot be deleted.']);
        }
        $postCategory->delete();
        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
