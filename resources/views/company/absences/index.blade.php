@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Absences - {{ $company->name }}</h2>
        <a class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('company.absences.create', $company) }}">Register Absence</a>
    </div>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="pb-2">User</th>
                <th class="pb-2">Date</th>
                <th class="pb-2">Reason</th>
                <th class="pb-2">Registered By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absences as $absence)
                <tr class="border-t border-slate-100">
                    <td class="py-2">{{ $absence->user?->name ?? $absence->user_id }}</td>
                    <td class="py-2">{{ $absence->date?->toDateString() }}</td>
                    <td class="py-2">{{ $absence->reason ?? '-' }}</td>
                    <td class="py-2">{{ $absence->creator?->name ?? $absence->created_by }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $absences->links() }}</div>
</div>
@endsection
