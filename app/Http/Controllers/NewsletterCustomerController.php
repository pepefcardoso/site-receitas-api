<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterCustomer\StoreRequest;
use App\Http\Resources\NewsletterCustomer\NewsletterCustomerResource;
use App\Models\NewsletterCustomer;
use App\Services\NewsletterCustomer\CreateNewsletterCustomer;
use App\Services\NewsletterCustomer\DeleteNewsletterCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsletterCustomerController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', NewsletterCustomer::class);
        return NewsletterCustomerResource::collection(NewsletterCustomer::latest()->paginate());
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
        $service->delete($newsletter->id);
        return response()->json(null, 204);
    }
}
