<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterCustomer\StoreRequest;
use App\Http\Resources\NewsletterCustomer\NewsletterCustomerResource;
use App\Models\NewsletterCustomer;
use App\Services\NewsletterCustomer\CreateNewsletterCustomer;
use App\Services\NewsletterCustomer\DeleteNewsletterCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class NewsletterCustomerController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', NewsletterCustomer::class);

        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        $customers = NewsletterCustomer::orderBy($orderBy, $orderDirection)->paginate($perPage);

        return NewsletterCustomerResource::collection($customers);
    }

    public function store(StoreRequest $request, CreateNewsletterCustomer $service): JsonResponse
    {
        $customer = $service->create($request->validated());
        return (new NewsletterCustomerResource($customer))->response()->setStatusCode(201);
    }

    public function show(NewsletterCustomer $newsletter): NewsletterCustomerResource
    {
        $this->authorize('view', $newsletter);
        return new NewsletterCustomerResource($newsletter);
    }

    public function destroy(NewsletterCustomer $newsletter, DeleteNewsletterCustomer $service): JsonResponse
    {
        $this->authorize('delete', $newsletter);
        $service->delete($newsletter);
        return response()->json(null, 204);
    }
}
