<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class FilterPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Post::class);
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:post_categories,id',
            'order_by' => 'nullable|string|in:title,created_at',
            'order_direction' => 'nullable|string|in:asc,desc',
            'user_id' => 'nullable|integer|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
