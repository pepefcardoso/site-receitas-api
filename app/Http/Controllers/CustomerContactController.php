<?php

namespace App\Http\Controllers;

use App\Models\CustomerContact;
use App\Services\CustomerContact\CreateCustomerContact;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class CustomerContactController extends BaseController
{
    public function register(Request $request, CreateCustomerContact $service)
    {
        $data = $request->validate(CustomerContact::rules());

        try {
            $customerContact = $service->create($data);

            return response()->json($customerContact, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
