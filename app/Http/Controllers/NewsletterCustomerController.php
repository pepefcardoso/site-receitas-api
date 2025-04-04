<?php

namespace App\Http\Controllers;

use App\Models\NewsletterCustomer;
use App\Services\NewsletterCustomer\CreateNewsletterCustomer;
use App\Services\NewsletterCustomer\DeleteNewsletterCustomer;
use App\Services\NewsletterCustomer\ListNewsletterCustomer;
use App\Services\NewsletterCustomer\ShowNewsletterCustomer;
use App\Services\NewsletterCustomer\UpdateNewsletterCustomer;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewsletterCustomerController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request, ListNewsletterCustomer $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $customers = $service->list($perPage);
            return response()->json($customers);
        });
    }

    public function store(Request $request, CreateNewsletterCustomer $service)
    {
        return $this->execute(function () use ($request, $service) {
            $data = $request->validate(NewsletterCustomer::rules());
            $customer = $service->create($data);
            return response()->json($customer, 201);
        });
    }

    public function show(NewsletterCustomer $customer, ShowNewsletterCustomer $service)
    {
        return $this->execute(function () use ($customer, $service) {
            $this->authorize('view', $customer);
            $customer = $service->show($customer->id);
            return response()->json($customer);
        });
    }

    public function update(Request $request, NewsletterCustomer $customer, UpdateNewsletterCustomer $service)
    {
        return $this->execute(function () use ($request, $customer, $service) {
            $this->authorize('update', $customer);
            $data = $request->validate(NewsletterCustomer::rules());
            $customer = $service->update($customer->id, $data);
            return response()->json($customer);
        });
    }

    public function destroy(NewsletterCustomer $customer, DeleteNewsletterCustomer $service)
    {
        return $this->execute(function () use ($customer, $service) {
            $this->authorize('delete', $customer);
            $response = $service->delete($customer->id);
            return response()->json($response);
        });
    }
}
