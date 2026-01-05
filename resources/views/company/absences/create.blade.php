@extends('layouts.app')

@section('content')
<div class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Register Absence - {{ $company->name }}</h2>
        <a class="rounded-lg bg-slate-600 px-3 py-1.5 text-sm font-semibold text-white" href="{{ route('company.absences.index', $company) }}">Back</a>
    </div>
    <form method="POST" action="{{ route('company.absences.store', $company) }}" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Employee</label>
            <select name="user_id" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">Select employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Date</label>
            <input type="date" name="date" value="{{ old('date') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Reason</label>
            <textarea name="reason" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('reason') }}</textarea>
        </div>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Save</button>
    </form>
</div>
@endsection
