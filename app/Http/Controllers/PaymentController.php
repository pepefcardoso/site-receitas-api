<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class PaymentController extends BaseController
{
    use ManagesResourceCaching;

    protected function getCacheTag(): string
    {
        return 'payments';
    }

    public function index(Request $request): JsonResource
    {
        $this->authorize('viewAny', Payment::class);

        $baseQuery = Payment::query();
        $relations = ['subscription', 'method'];

        $payments = $this->getCachedAndPaginated($request, $baseQuery, $relations, 'created_at');

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): JsonResource
    {
        $payment = Payment::create($request->validated());
        $this->flushResourceCache();

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
        $this->flushResourceCache();

        $payment->load('method');
        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment): Response
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        $this->flushResourceCache();

        return response()->noContent();
    }
}
