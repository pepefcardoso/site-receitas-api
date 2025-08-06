<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subscription = $this->route('subscription');
        return $this->user()->can('update', $subscription);
    }

    public function rules(): array
    {
        return [
            'plan_id' => 'sometimes|required|exists:plans,id',
            'starts_at' => 'sometimes|required|date',
            'ends_at' => 'sometimes|required|date|after:starts_at',
            'status' => 'sometimes|required|in:active,canceled,expired',
        ];
    }
}
