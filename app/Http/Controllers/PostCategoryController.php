<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostCategoryRequest;
use App\Http\Requests\UpdatePostCategoryRequest;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    public function index()
    {
        $categories = PostCategory::all();
        return response()->json($categories, 201);
    }

    public function store(Request $request)
    {
        $data = $request->validate(PostCategory::$rules);

        $postCategory = PostCategory::create($data);

        return response()->json($postCategory, 201);
    }

    public function show(PostCategory $postCategory)
    {
        return response()->json($postCategory, 201);
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        $data = $request->validate(PostCategory::$rules);

        $postCategory->update($data);

        return response()->json($postCategory, 201);
    }

    public function destroy(PostCategory $postCategory)
    {
        $postCategory->delete();

        return response()->json(null, 204);
    }
}
