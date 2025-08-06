<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->payment);
    }

    public function rules(): array
    {
        return [
            'amount' => 'sometimes|required|numeric|gt:0',
            'status' => ['sometimes', 'required', 'string', Rule::in(['pending', 'paid', 'failed'])],
            'due_date' => 'sometimes|required|date',
            'paid_at' => 'nullable|date|required_if:status,paid',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
