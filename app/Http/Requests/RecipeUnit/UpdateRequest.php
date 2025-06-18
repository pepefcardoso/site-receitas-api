<?php

namespace App\Http\Requests\RecipeUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $unit = $this->route('recipe_unit');
        return $this->user()->can('update', $unit);
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['normalized_name' => Str::upper($this->name)]);
        }
    }

    public function rules(): array
    {
        $unitId = $this->route('recipe_unit')->id;

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('recipe_units')->ignore($unitId)],
            'normalized_name' => ['required', 'string', 'max:50', Rule::unique('recipe_units')->ignore($unitId)],
        ];
    }
}
