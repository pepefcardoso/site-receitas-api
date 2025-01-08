<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use Illuminate\Http\Request;

class RecipeStepController extends Controller
{
    public function index()
    {
        $categories = RecipeStep::all();
        return response()->json($categories, 201);
    }

    public function store(Request $request)
    {
        $data = $request->validate(RecipeStep::rules());

        $RecipeStep = RecipeStep::create($data);

        return response()->json($RecipeStep, 201);
    }

    public function show(RecipeStep $RecipeStep)
    {
        return response()->json($RecipeStep, 201);
    }

    public function update(Request $request, RecipeStep $RecipeStep)
    {
        $data = $request->validate(RecipeStep::rules());

        $RecipeStep->update($data);

        return response()->json($RecipeStep, 201);
    }

    public function destroy(RecipeStep $RecipeStep)
    {
        $RecipeStep->delete();

        return response()->json(null, 204);
    }
}
