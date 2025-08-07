<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class PaymentController extends BaseController
{
    public function index(Request $request): JsonResource
    {
        $this->authorize('viewAny', Payment::class);

        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        $payments = Payment::with(['subscription', 'method'])
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): JsonResource
    {
        $payment = Payment::create($request->validated());
        $payment->load('method');
        return new PaymentResource($payment);
    }

    public function show(Payment $payment): JsonResource
    {
        $this->authorize('view', $payment);
        $payment->load(['subscription', 'method']);
        return new PaymentResource($payment);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResource
    {
        $payment->update($request->validated());
        $payment->load('method');
        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment): Response
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        return response()->noContent();
    }
}
