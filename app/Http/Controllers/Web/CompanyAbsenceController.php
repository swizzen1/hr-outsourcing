<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Absence;
use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Actions\Absences\CreateAbsence;
use App\Actions\Support\ActionException;
use App\Http\Requests\StoreAbsenceRequest;

class CompanyAbsenceController extends Controller
{
    public function index(Company $company)
    {
        Gate::authorize('view', $company);

        $absences = Absence::query()
            ->where('company_id', $company->id)
            ->with(['user', 'creator'])
            ->orderByDesc('date')
            ->paginate(15);

        return view('company.absences.index', [
            'company' => $company,
            'absences' => $absences,
        ]);
    }

    public function create(Company $company)
    {
        Gate::authorize('view', $company);
        Gate::authorize('create', [Absence::class, $company]);

        $employees = User::query()
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('company.absences.create', [
            'company' => $company,
            'employees' => $employees,
        ]);
    }

    public function store(StoreAbsenceRequest $request, Company $company, CreateAbsence $createAbsence)
    {
        Gate::authorize('view', $company);
        Gate::authorize('create', [Absence::class, $company]);

        try {
            $createAbsence->handle($request->user(), $company, $request->validated());
        } catch (ActionException $exception) {
            return back()->withErrors([$exception->getField() ?? 'absence' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('company.absences.index', $company)
            ->with('status', 'Absence registered.');
    }
}
