<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\Subscription\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Subscription::class, 'subscription');
    }

    public function index()
    {
        $subscriptions = Subscription::with(['company', 'plan'])->paginate();
        return SubscriptionResource::collection($subscriptions);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $existingSubscription = Subscription::where('company_id', $data['company_id'])
                ->where('status', 'active')
                ->first();
            if ($existingSubscription) {
                throw new Exception('This company already has an active subscription.');
            }
            $subscription = Subscription::create($validatedData);
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
        $updatedSubscription =$subscription->update($request->validated());
        return new SubscriptionResource($updatedSubscription);
    }

    public function destroy(Subscription $subscription): Response
    {
        $subscription->delete();
        return response()->noContent();
    }
}
