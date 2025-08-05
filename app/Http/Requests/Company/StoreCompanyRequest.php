<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Company::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'size:18', Rule::unique('companies')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('companies')],
            'phone' => ['required', 'string', 'regex:/^\d{10,11}$/', Rule::unique('companies')],
            'address' => ['required', 'string', 'max:255'],
            'website' => ['required', 'string', 'max:255'],
        ];
    }
}
