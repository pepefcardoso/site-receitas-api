<?php

namespace App\Http\Requests\RecipeCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\RecipeCategory::class);
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['normalized_name' => Str::upper($this->name)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:recipe_categories',
            'normalized_name' => 'required|string|max:50|unique:recipe_categories',
        ];
    }
}
