<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class FilterCompaniesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Company::class);
    }
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'order_by' => 'nullable|string|in:name,cnpj,phone,email,created_at',
            'order_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
