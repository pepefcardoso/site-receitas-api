<?php

namespace App\Http\Requests\PostTopic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $topic = $this->route('post_topic');
        return $this->user()->can('update', $topic);
    }
    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['normalized_name' => Str::upper($this->name)]);
        }
    }
    public function rules(): array
    {
        $topicId = $this->route('post_topic')->id;
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('post_topics')->ignore($topicId)],
            'normalized_name' => ['required', 'string', 'max:50', Rule::unique('post_topics')->ignore($topicId)],
        ];
    }
}
