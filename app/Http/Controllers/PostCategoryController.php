<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryController extends Controller
{
    public function index()
    {
        $categories = PostCategory::all();
        return response()->json($categories, 201);
    }

    public function store(Request $request)
    {
        $request["normalized_name"] = Str::upper($request->name);
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
        $request["normalized_name"] = Str::upper($request->name);
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
