<?php

namespace App\Http\Controllers;

use App\Models\NewsletterCustomer;
use App\Services\NewsletterCustomer\DeleteNewsletterCustomer;
use App\Services\NewsletterCustomer\ListNewsletterCustomer;
use App\Services\NewsletterCustomer\ShowNewsletterCustomer;
use App\Services\NewsletterCustomer\UpdateNewsletterCustomer;
use CreateNewsletterCustomer;
use Illuminate\Routing\Controller as BaseController;
use Request;

class NewsletterCustomerController extends BaseController
{
    public function index(ListNewsletterCustomer $service)
    {
        $perPage = Request::get('per_page', 10);

        $customers = $service->list($perPage);

        return response()->json($customers);
    }

    public function store(Request $request, CreateNewsletterCustomer $service)
    {
        $data = $request->validate(NewsletterCustomer::rules());

        try {
            $customer = $service->create($data);
            return response()->json($customer, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(NewsletterCustomer $customer, ShowNewsletterCustomer $service)
    {
        $customer = $service->show($customer->id);

        return response()->json($customer);
    }

    public function update(Request $request, NewsletterCustomer $customer, UpdateNewsletterCustomer $service)
    {
        $data = $request->validate(NewsletterCustomer::rules());

        try {
            $customer = $service->update($customer, $data);
            return response()->json($customer, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(NewsletterCustomer $customer, DeleteNewsletterCustomer $service)
    {
        try {
            $response = $service->delete($customer);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
