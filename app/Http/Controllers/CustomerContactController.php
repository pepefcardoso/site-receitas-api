<?php

namespace App\Http\Controllers;

use App\Enum\CustomerContactStatusEnum;
use App\Models\CustomerContact;
use App\Services\CustomerContact\CreateCustomerContact;
use App\Services\CustomerContact\UpdateCustomerContactStatus;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class CustomerContactController extends BaseController
{
    use AuthorizesRequests;

    public function register(Request $request, CreateCustomerContact $service)
    {
        return $this->execute(function () use ($request, $service) {
            $data = $request->validate(CustomerContact::rules());
            $customerContact = $service->create($data);
            return response()->json($customerContact, 201);
        });
    }

    public function updateStatus(Request $request, UpdateCustomerContactStatus $service, $contactId)
    {
        return $this->execute(function () use ($request, $service, $contactId) {
            $data = $request->validate([
                'status' => [
                    'required',
                    'integer',
                    Rule::in(array_map(fn($case) => $case->value, CustomerContactStatusEnum::cases()))
                ],
            ]);

            $updatedContact = $service->update($contactId, $data['status']);
            return response()->json($updatedContact);
        });
    }
}
