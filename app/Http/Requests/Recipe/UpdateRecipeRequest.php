<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $recipe = $this->route('recipe');
        return $this->user()->can('update', $recipe);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = (new StoreRecipeRequest())->rules();
        $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        return $rules;
    }
}
