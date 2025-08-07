<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\Subscription\SubscriptionResource;
use App\Models\Subscription;
use App\Notifications\SubscribedToPlan;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionController extends BaseController
{
    public function __construct()
    {
        $this->authorizeResource(Subscription::class, 'subscription');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        $subscriptions = Subscription::with(['company', 'plan'])
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);

        return SubscriptionResource::collection($subscriptions);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $existingSubscription = Subscription::where('company_id', $validatedData['company_id'])
                ->whereIsActive()
                ->first();
            if ($existingSubscription) {
                throw new Exception('This company already has an active subscription.');
            }
            $subscription = Subscription::create($validatedData);
            $subscription->company->notify(new SubscribedToPlan($subscription));
            return (new SubscriptionResource($subscription))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(Subscription $subscription): SubscriptionResource
    {
        return new SubscriptionResource($subscription->load(['company', 'plan']));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): SubscriptionResource
    {
        $updatedSubscription = $subscription->update($request->validated());
        return new SubscriptionResource($updatedSubscription);
    }

    public function destroy(Subscription $subscription): Response
    {
        $subscription->delete();
        return response()->noContent();
    }
}
