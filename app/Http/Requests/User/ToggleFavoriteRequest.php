<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'post_id' => 'sometimes|required|exists:posts,id',
            'recipe_id' => 'sometimes|required|exists:recipes,id',
        ];
    }
}
