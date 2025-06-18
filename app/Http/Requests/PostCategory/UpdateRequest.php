<?php

namespace App\Http\Requests\PostCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('post_category');
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
        $categoryId = $this->route('post_category')->id;
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('post_categories')->ignore($categoryId)],
            'normalized_name' => ['required', 'string', 'max:50', Rule::unique('post_categories')->ignore($categoryId)],
        ];
    }
}
