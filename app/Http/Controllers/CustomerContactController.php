<?php

namespace App\Http\Controllers;

use App\Models\CustomerContact;
use App\Services\CustomerContact\CreateCustomerContact;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
}
