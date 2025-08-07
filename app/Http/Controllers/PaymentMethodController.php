<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentMethodController extends Controller
{
    /**
     * Retorna uma lista dos métodos de pagamento ativos.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
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
