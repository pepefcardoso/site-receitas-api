<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerContact\StoreRequest;
use App\Http\Requests\CustomerContact\UpdateStatusRequest;
use App\Http\Resources\CustomerContact\CustomerContactResource;
use App\Models\CustomerContact;
use App\Services\CustomerContact\CreateCustomerContact;
use App\Services\CustomerContact\UpdateCustomerContactStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerContactController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerContact::class);

        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        $contacts = CustomerContact::orderBy($orderBy, $orderDirection)->paginate($perPage);
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

    public function updateStatus(UpdateStatusRequest $request, CustomerContact $customerContact, UpdateCustomerContactStatus $service): CustomerContactResource
    {
        $this->authorize('update', $customerContact);
        $updatedContact = $service->update($customerContact, $request->validated('status'));
        return new CustomerContactResource($updatedContact);
    }
}
