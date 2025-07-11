<?php

namespace App\Http\Requests\NewsletterCustomer;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['email' => 'required|email|max:255|unique:newsletter_customers,email'];
    }
}
