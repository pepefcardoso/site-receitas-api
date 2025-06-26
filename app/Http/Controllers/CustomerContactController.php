<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerContact\StoreRequest;
use App\Http\Requests\CustomerContact\UpdateStatusRequest;
use App\Http\Resources\CustomerContact\CustomerContactResource;
use App\Models\CustomerContact;
use App\Services\CustomerContact\CreateCustomerContact;
use App\Services\CustomerContact\UpdateCustomerContactStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerContactController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerContact::class);
        $contacts = CustomerContact::latest()->paginate();
        return CustomerContactResource::collection($contacts);
    }

    public function store(StoreRequest $request, CreateCustomerContact $service): JsonResponse
    {
        $contact = $service->create($request->validated());
        return (new CustomerContactResource($contact))->response()->setStatusCode(201);
    }

    public function show(CustomerContact $customer_contact): CustomerContactResource
    {
        $this->authorize('view', $customer_contact);
        return new CustomerContactResource($customer_contact);
    }

    public function updateStatus(UpdateStatusRequest $request, CustomerContact $customer_contact, UpdateCustomerContactStatus $service): CustomerContactResource
    {
        $updatedContact = $service->update($customer_contact->id, $request->validated('status'));
        return new CustomerContactResource($updatedContact);
    }
}
