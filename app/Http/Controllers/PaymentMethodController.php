<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentMethodController extends BaseController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', PaymentMethod::class);

        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'name');
        $orderDirection = $request->input('order_direction', 'asc');

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);

        return PaymentMethodResource::collection($paymentMethods);
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $paymentMethod = PaymentMethod::create($request->validated());
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
        return new PaymentMethodResource($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        $this->authorize('delete', $paymentMethod);
        $paymentMethod->delete();
        return response()->json(null, 204);
    }
}
