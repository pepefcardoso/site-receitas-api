<?php

namespace App\Http\Controllers;

use App\Models\RecipeIngredient;
use Illuminate\Http\Request;

class RecipeIngredientController extends Controller
{
    public function index()
    {
        $categories = RecipeIngredient::all();
        return response()->json($categories, 201);
    }

    public function store(Request $request)
    {
        $data = $request->validate(RecipeIngredient::rules());

        $RecipeIngredient = RecipeIngredient::create($data);

        return response()->json($RecipeIngredient, 201);
    }

    public function show(RecipeIngredient $RecipeIngredient)
    {
        return response()->json($RecipeIngredient, 201);
    }

    public function update(Request $request, RecipeIngredient $RecipeIngredient)
    {
        $data = $request->validate(RecipeIngredient::rules());

        $RecipeIngredient->update($data);

        return response()->json($RecipeIngredient, 201);
    }

    public function destroy(RecipeIngredient $RecipeIngredient)
    {
        $RecipeIngredient->delete();

        return response()->json(null, 204);
    }
}
