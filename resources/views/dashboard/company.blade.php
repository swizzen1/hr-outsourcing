@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold">Company Admin Dashboard</h2>
    <p class="mt-1 text-sm text-slate-600">{{ $company->name }}</p>
    <p class="mt-3 text-sm">Vacancies: <strong>{{ $vacancies }}</strong> | Pending leave requests: <strong>{{ $pendingLeaves }}</strong></p>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="text-lg font-semibold">Quick actions</h3>
    <div class="mt-4 flex flex-wrap gap-3">
        <a class="inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('company.vacancies.create', $company) }}">Create Vacancy</a>
        <a class="inline-flex rounded-lg bg-slate-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('company.leave_requests.index', $company) }}">Review Leave Requests</a>
        <a class="inline-flex rounded-lg bg-slate-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('company.absences.create', $company) }}">Register Absence</a>
        <a class="inline-flex rounded-lg bg-slate-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('company.absences.index', $company) }}">View Absences</a>
    </div>
</div>
@endsection
