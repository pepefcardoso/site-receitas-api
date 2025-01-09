<?php

namespace App\Http\Controllers;

use App\Models\RecipeDiet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecipeDietController extends Controller
{
    public function index()
    {
        $diets = RecipeDiet::all();
        return response()->json($diets, 201);
    }

    public function store(Request $request)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        $RecipeDiet = RecipeDiet::create($data);

        return response()->json($RecipeDiet, 201);
    }

    public function show(RecipeDiet $RecipeDiet)
    {
        return response()->json($RecipeDiet, 201);
    }

    public function update(Request $request, RecipeDiet $RecipeDiet)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        $RecipeDiet->update($data);

        return response()->json($RecipeDiet, 201);
    }

    public function destroy(RecipeDiet $RecipeDiet)
    {
        $RecipeDiet->delete();

        return response()->json(null, 204);
    }
}
