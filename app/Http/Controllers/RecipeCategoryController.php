<?php

namespace App\Http\Controllers;

use App\Models\RecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecipeCategoryController extends Controller
{
    public function index()
    {
        $categories = RecipeCategory::all();
        return response()->json($categories, 201);
    }

    public function store(Request $request)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeCategory::$rules);

        $RecipeCategory = RecipeCategory::create($data);

        return response()->json($RecipeCategory, 201);
    }

    public function show(RecipeCategory $RecipeCategory)
    {
        return response()->json($RecipeCategory, 201);
    }

    public function update(Request $request, RecipeCategory $RecipeCategory)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeCategory::$rules);

        $RecipeCategory->update($data);

        return response()->json($RecipeCategory, 201);
    }

    public function destroy(RecipeCategory $RecipeCategory)
    {
        $RecipeCategory->delete();

        return response()->json(null, 204);
    }
}
