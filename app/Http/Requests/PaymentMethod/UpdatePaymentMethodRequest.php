<?php

namespace App\Http\Requests\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        $paymentMethod = $this->route('payment_method');
        return $this->user()->can('update', $paymentMethod);
    }

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
