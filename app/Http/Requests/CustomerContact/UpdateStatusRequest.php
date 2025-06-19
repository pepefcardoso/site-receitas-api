<?php

namespace App\Http\Requests\CustomerContact;

use App\Enum\CustomerContactStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('customer_contact'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'integer', Rule::in(array_column(CustomerContactStatusEnum::cases(), 'value'))],
        ];
    }
}
