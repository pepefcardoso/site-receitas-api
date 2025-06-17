<?php

namespace App\Http\Requests\RecipeCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('recipe_category');
        return $this->user()->can('update', $category);
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['normalized_name' => Str::upper($this->name)]);
        }
    }

    public function rules(): array
    {
        $categoryId = $this->route('recipe_category')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('recipe_categories')->ignore($categoryId),
            ],
            'normalized_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('recipe_categories')->ignore($categoryId),
            ],
        ];
    }
}
