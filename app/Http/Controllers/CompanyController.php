<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\FilterCompaniesRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Models\Company;
use App\Services\Company\ListCompanies;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController
{
    use AuthorizesRequests;

    public function index(FilterCompaniesRequest $request, ListCompanies $service): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Company::class);
        $companies = $service->list($request->validated());
        return CompanyResource::collection($companies);
    }

    public function store(StoreCompanyRequest $request)
    {
        $this->authorize('create', Company::class);
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $company = Company::create($validatedData);
        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Company $company): CompanyResource
    {
        $this->authorize('view', $company);
        $company->load('subscriptions.plan');
        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        $this->authorize('update', $company);
        $company->update($request->validated());
        return new CompanyResource($company);
    }

    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);
        $company->delete();
        return response()->json(null, 204);
    }
}
