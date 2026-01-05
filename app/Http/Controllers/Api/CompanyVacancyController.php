<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Vacancies\CreateVacancy;
use App\Http\Requests\StoreVacancyRequest;
use App\Http\Resources\VacancyPublicResource;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Gate;

class CompanyVacancyController extends Controller
{
    public function store(StoreVacancyRequest $request, Company $company, CreateVacancy $createVacancy)
    {
        Gate::authorize('create', [Vacancy::class, $company]);

        $vacancy = $createVacancy->handle($company, $request->validated());

        return (new VacancyPublicResource($vacancy))
            ->response()
            ->setStatusCode(201);
    }
}
