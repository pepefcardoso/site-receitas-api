<?php

namespace App\Http\Controllers;

use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\Plan\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Plan::class, 'plan');
    }

    public function index(Request $request): JsonResource
    {
        $perPage = $request->input('per_page', 10);
        $plans = Plan::filter($request->all())->paginate($perPage);
        return PlanResource::collection($plans);
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = Plan::create($request->validated());

        return response()->json(new PlanResource($plan), Response::HTTP_CREATED);
    }

    public function show(Plan $plan): PlanResource
    {
        return new PlanResource($plan);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): PlanResource
    {
        $plan->update($request->validated());

        return new PlanResource($plan);
    }

    public function destroy(Plan $plan): Response
    {
        $plan->delete();

        return response()->noContent();
    }
}
