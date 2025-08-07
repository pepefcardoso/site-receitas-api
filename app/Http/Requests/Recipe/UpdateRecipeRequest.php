<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $recipe = $this->route('recipe');
        return $this->user()->can('update', $recipe);
    }

    public function rules(): array
    {
        $rules = (new StoreRecipeRequest())->rules();
        $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        return $rules;
    }
}
