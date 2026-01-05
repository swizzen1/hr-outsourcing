@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold">Admin/HR Dashboard</h2>
    <p class="mt-1 text-sm text-slate-600">Companies overview</p>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="pb-2">Company</th>
                <th class="pb-2">Users</th>
                <th class="pb-2">Vacancies</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr class="border-t border-slate-100">
                    <td class="py-2">{{ $company->name }}</td>
                    <td class="py-2">{{ $company->users_count }}</td>
                    <td class="py-2">{{ $company->vacancies_count }}</td>
                    <td class="py-2">
                        <a class="mr-2 inline-flex rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white" href="{{ route('company.vacancies.index', $company) }}">Vacancies</a>
                        <a class="mr-2 inline-flex rounded-lg bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white" href="{{ route('company.leave_requests.index', $company) }}">Leave Requests</a>
                        <a class="inline-flex rounded-lg bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white" href="{{ route('company.absences.index', $company) }}">Absences</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
