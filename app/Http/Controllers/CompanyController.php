<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\FilterCompaniesRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Services\Company\DeleteCompany;
use App\Services\Company\UpdateCompany;
use App\Services\Company\CreateCompany;
use App\Models\Company;
use App\Services\Company\ListCompanies;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends BaseController
{

    public function index(FilterCompaniesRequest $request, ListCompanies $service): AnonymousResourceCollection
    {
        $companies = $service->list($request->validated());

        return CompanyResource::collection($companies);
    }

    public function store(StoreCompanyRequest $request, CreateCompany $service): JsonResponse
    {
        $company = $service->create($request->validated());

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }

    public function myCompany(Request $request): CompanyResource | JsonResponse
    {
        $user = $request->user();
        $company = $user->company;

        if (!$company) {
            return response()->json(['message' => 'Nenhuma empresa encontrada para este usuário.'], 404);
        }

        $this->authorize('view', $company);

        $company->load('subscriptions.plan', 'image');

        return new CompanyResource($company);
    }

    public function show(Company $company): CompanyResource
    {
        $this->authorize('view', $company);

        $company->load('subscriptions.plan', 'image');

        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, Company $company, UpdateCompany $service): CompanyResource
    {
        $company = $service->update($company, $request->validated());

        return new CompanyResource($company);
    }

    public function destroy(Company $company, DeleteCompany $service)
    {
        $this->authorize('delete', $company);

        $service->delete($company);

        return response()->json(null, 204);
    }
}
