<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class FilterUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', User::class);
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'role' => 'nullable|array',
            'role.*' => 'integer',
            'birthday_start' => 'nullable|date',
            'birthday_end' => 'nullable|date|after_or_equal:birthday_start',
            'order_by' => 'nullable|string|in:name,email,created_at',
            'order_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
