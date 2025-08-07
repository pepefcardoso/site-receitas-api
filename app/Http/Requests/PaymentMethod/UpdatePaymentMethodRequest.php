<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $paymentMethod = $this->route('payment_method');
        return $this->user()->can('update', $paymentMethod);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $paymentMethod = $this->route('payment_method');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('payment_methods')->ignore($paymentMethod)],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('payment_methods')->ignore($paymentMethod)],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
