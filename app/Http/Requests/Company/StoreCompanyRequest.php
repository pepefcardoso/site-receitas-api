<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Company::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'size:18', Rule::unique('companies')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('companies')],
            'phone' => ['required', 'string', 'regex:/^\d{10,11}$/', Rule::unique('companies')],
            'address' => ['required', 'string', 'max:255'],
            'website' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
