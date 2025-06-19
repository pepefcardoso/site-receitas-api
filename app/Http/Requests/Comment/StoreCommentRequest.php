<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create');
    }

    public function rules(): array
    {
        return ['content' => 'required|string|max:1000'];
    }
}
