<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\Plan\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PlanController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->authorizeResource(Plan::class, 'plan');
    }

    protected function getCacheTag(): string
    {
        return 'plans';
    }

    public function index(Request $request): JsonResource
    {
        $cacheKey = 'plans:list:' . http_build_query($request->all());

        $plans = Cache::tags($this->getCacheTag())->remember($cacheKey, now()->addHour(), function () use ($request) {
            $perPage = $request->input('per_page', 10);
            return Plan::filter($request->all())->paginate($perPage);
        });

        return PlanResource::collection($plans);
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = Plan::create($request->validated());
        $this->flushResourceCache();

        return response()->json(new PlanResource($plan), Response::HTTP_CREATED);
    }

    public function show(Plan $plan): PlanResource
    {
        return new PlanResource($plan);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): PlanResource
    {
        $plan->update($request->validated());
        $this->flushResourceCache();

        return new PlanResource($plan);
    }

    public function destroy(Plan $plan): Response
    {
        $plan->delete();
        $this->flushResourceCache();

        return response()->noContent();
    }
}
