<?php

namespace App\Http\Requests\RecipeDiet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $diet = $this->route('recipe_diet');
        return $this->user()->can('update', $diet);
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['normalized_name' => Str::upper($this->name)]);
        }
    }

    public function rules(): array
    {
        $dietId = $this->route('recipe_diet')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('recipe_diets')->ignore($dietId),
            ],
            'normalized_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('recipe_diets')->ignore($dietId),
            ],
        ];
    }
}
