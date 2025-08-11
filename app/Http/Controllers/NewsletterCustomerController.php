<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\NewsletterCustomer\StoreRequest;
use App\Http\Resources\NewsletterCustomer\NewsletterCustomerResource;
use App\Models\NewsletterCustomer;
use App\Services\NewsletterCustomer\CreateNewsletterCustomer;
use App\Services\NewsletterCustomer\DeleteNewsletterCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsletterCustomerController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    protected function getCacheTag(): string
    {
        return 'newsletter_customers';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', NewsletterCustomer::class);

        $customers = $this->getCachedAndPaginated($request, NewsletterCustomer::query(), [], 'created_at');

        return NewsletterCustomerResource::collection($customers);
    }

    public function store(StoreRequest $request, CreateNewsletterCustomer $service): JsonResponse
    {
        $customer = $service->create($request->validated());
        $this->flushResourceCache();

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
        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
