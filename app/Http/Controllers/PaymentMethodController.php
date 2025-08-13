<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentMethodController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'payment_methods';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', PaymentMethod::class);

        $baseQuery = PaymentMethod::query()->where('is_active', true);

        $paymentMethods = $this->getCachedAndPaginated($request, $baseQuery);

        return PaymentMethodResource::collection($paymentMethods);
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $paymentMethod = PaymentMethod::create($request->validated());
        $this->flushResourceCache();

        return (new PaymentMethodResource($paymentMethod))
            ->response()
            ->setStatusCode(201);
    }

    public function show(PaymentMethod $paymentMethod): PaymentMethodResource
    {
        $this->authorize('view', $paymentMethod);

        return new PaymentMethodResource($paymentMethod);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): PaymentMethodResource
    {
        $paymentMethod->update($request->validated());
        $this->flushResourceCache();

        return new PaymentMethodResource($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        $this->authorize('delete', $paymentMethod);

        $paymentMethod->delete();
        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
