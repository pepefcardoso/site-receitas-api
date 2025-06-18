<?php

namespace App\Http\Requests\Recipe;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Recipe::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer|min:1',
            'portion' => 'required|integer|min:1',
            'difficulty' => ['required', 'integer', Rule::enum(RecipeDifficultyEnum::class)],
            'category_id' => 'required|exists:recipe_categories,id',
            'diets' => 'required|array',
            'diets.*' => 'exists:recipe_diets,id',
            'steps' => 'required|array',
            'steps.*.description' => 'required|string',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'nullable|integer|exists:recipe_ingredients,id',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit_id' => 'required|exists:recipe_units,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
