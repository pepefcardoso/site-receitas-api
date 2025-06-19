<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comment = $this->route('comment');
        return $this->user()->can('update', $comment);
    }

    public function rules(): array
    {
        return ['content' => 'required|string|max:1000'];
    }
}
