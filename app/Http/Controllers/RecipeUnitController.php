<?php

namespace App\Http\Controllers;

use App\Models\RecipeUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecipeUnitController extends Controller
{
    public function index()
    {
        $units = RecipeUnit::all();
        return response()->json($units, 201);
    }

    public function store(Request $request)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeUnit::$rules);

        $RecipeUnit = RecipeUnit::create($data);

        return response()->json($RecipeUnit, 201);
    }

    public function show(RecipeUnit $RecipeUnit)
    {
        return response()->json($RecipeUnit, 201);
    }

    public function update(Request $request, RecipeUnit $RecipeUnit)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeUnit::$rules);

        $RecipeUnit->update($data);

        return response()->json($RecipeUnit, 201);
    }

    public function destroy(RecipeUnit $RecipeUnit)
    {
        $RecipeUnit->delete();

        return response()->json(null, 204);
    }
}
