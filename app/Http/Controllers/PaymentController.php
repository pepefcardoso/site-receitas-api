<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class PaymentController
{
    public function index(): JsonResource
    {
        $this->authorize('viewAny', Payment::class);
        $payments = Payment::with('subscription')->latest()->paginate(15);
        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): JsonResource
    {
        $payment = Payment::create($request->validated());
        return new PaymentResource($payment);
    }

    public function show(Payment $payment): JsonResource
    {
        $this->authorize('view', $payment);
        $payment->load('subscription');
        return new PaymentResource($payment);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResource
    {
        $payment->update($request->validated());
        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment): Response
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        return response()->noContent();
    }
}
