<?php

namespace App\Http\Requests\User;

use App\Enum\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', User::class);
    }

    public function rules(): array
    {
        $roleValues = array_map(fn($case) => $case->value, RolesEnum::cases());

        return [
            'role' => ['required', Rule::in($roleValues)],
        ];
    }
}
