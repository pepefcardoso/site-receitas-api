<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\Subscription\SubscriptionResource;
use App\Models\Subscription;
use App\Notifications\SubscribedToPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SubscriptionController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->authorizeResource(Subscription::class, 'subscription');
    }

    protected function getCacheTag(): string
    {
        return 'subscriptions';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Subscription::class);

        $baseQuery = Subscription::query();
        $relations = ['company', 'plan'];

        $subscriptions = $this->getCachedAndPaginated($request, $baseQuery, $relations, 'created_at');

        return SubscriptionResource::collection($subscriptions);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $existingSubscription = Subscription::where('company_id', $validatedData['company_id'])
            ->whereIsActive()
            ->first();

        if ($existingSubscription) {
            throw ValidationException::withMessages([
                'company_id' => 'This company already has an active subscription.',
            ]);
        }

        $subscription = Subscription::create($validatedData);
        $subscription->company->notify(new SubscribedToPlan($subscription));

        $this->flushResourceCache();

        return (new SubscriptionResource($subscription))
            ->response()
            ->setStatusCode(SymfonyResponse::HTTP_CREATED);
    }

    public function show(Subscription $subscription): SubscriptionResource
    {
        $this->authorize('view', $subscription);

        return new SubscriptionResource($subscription->load(['company', 'plan']));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): SubscriptionResource
    {
        $subscription->update($request->validated());
        $this->flushResourceCache();

        return new SubscriptionResource($subscription);
    }

    public function destroy(Subscription $subscription): Response
    {
        $this->authorize('delete', $subscription);

        $subscription->delete();
        $this->flushResourceCache();

        return response()->noContent();
    }
}
