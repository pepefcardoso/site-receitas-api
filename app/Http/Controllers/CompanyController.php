<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\FilterCompaniesRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Services\Company\ListCompanies;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(FilterCompaniesRequest $request, ListCompanies $service): AnonymousResourceCollection
    {
        $companies = $service->list($request->validated());
        return CompanyResource::collection($companies);
    }

    public function store(StoreCompanyRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $company = Company::create($validatedData);
        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Company $company): CompanyResource
    {
        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        $company->update($request->validated());

        return new CompanyResource($company);
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(null, 204);
    }
}
