<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Actions\Vacancies\CreateVacancy;
use App\Http\Requests\StoreVacancyRequest;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Gate;

class CompanyVacancyController extends Controller
{
    public function index(Company $company)
    {
        Gate::authorize('view', $company);

        $vacancies = Vacancy::query()
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('company.vacancies.index', [
            'company' => $company,
            'vacancies' => $vacancies,
        ]);
    }

    public function create(Company $company)
    {
        Gate::authorize('view', $company);
        Gate::authorize('create', [Vacancy::class, $company]);

        return view('company.vacancies.create', [
            'company' => $company,
        ]);
    }

    public function store(StoreVacancyRequest $request, Company $company, CreateVacancy $createVacancy)
    {
        Gate::authorize('view', $company);
        Gate::authorize('create', [Vacancy::class, $company]);

        $createVacancy->handle($company, $request->validated());

        return redirect()
            ->route('company.vacancies.index', $company)
            ->with('status', 'Vacancy created.');
    }
}
