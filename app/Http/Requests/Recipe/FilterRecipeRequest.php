<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class FilterRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:recipe_categories,id',
            'diets' => 'nullable|array',
            'diets.*' => 'integer|exists:recipe_diets,id',
            'order_by' => 'nullable|string|in:title,created_at,time,difficulty',
            'order_direction' => 'nullable|string|in:asc,desc',
            'user_id' => 'nullable|integer|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
