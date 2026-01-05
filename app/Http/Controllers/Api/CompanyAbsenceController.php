<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Absences\CreateAbsence;
use App\Actions\Support\ActionException;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Resources\AbsenceResource;
use App\Models\Absence;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyAbsenceController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $query = Absence::query()->where('company_id', $company->id);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->query('to'));
        }

        $paginator = $query->orderByDesc('date')->paginate($perPage);

        return response()->json([
            'data' => AbsenceResource::collection($paginator->getCollection())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(StoreAbsenceRequest $request, Company $company, CreateAbsence $createAbsence)
    {
        Gate::authorize('create', [Absence::class, $company]);

        try {
            $absence = $createAbsence->handle($request->user(), $company, $request->validated());
        } catch (ActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return (new AbsenceResource($absence))
            ->response()
            ->setStatusCode(201);
    }
}
