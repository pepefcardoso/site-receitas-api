<?php

namespace App\Http\Requests\PostTopic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\PostTopic::class);
    }
    protected function prepareForValidation()
    {
        $this->merge(['normalized_name' => Str::upper($this->name)]);
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:post_topics',
            'normalized_name' => 'required|string|max:50|unique:post_topics',
        ];
    }
}
