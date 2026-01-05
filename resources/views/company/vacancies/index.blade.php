@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Vacancies - {{ $company->name }}</h2>
        <a class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('company.vacancies.create', $company) }}">Create Vacancy</a>
    </div>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="pb-2">Title</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Published At</th>
                <th class="pb-2">Expiration</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vacancies as $vacancy)
                <tr class="border-t border-slate-100">
                    <td class="py-2">{{ $vacancy->title }}</td>
                    <td class="py-2"><span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-xs">{{ $vacancy->status }}</span></td>
                    <td class="py-2">{{ $vacancy->published_at?->toDateTimeString() ?? '-' }}</td>
                    <td class="py-2">{{ $vacancy->expiration_date?->toDateString() ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $vacancies->links() }}</div>
</div>
@endsection
