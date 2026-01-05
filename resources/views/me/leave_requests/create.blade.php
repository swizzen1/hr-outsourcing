@extends('layouts.app')

@section('content')
<div class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">New Leave Request</h2>
        <a class="rounded-lg bg-slate-600 px-3 py-1.5 text-sm font-semibold text-white" href="{{ route('me.leave_requests.index') }}">Back</a>
    </div>
    <form method="POST" action="{{ route('me.leave_requests.store') }}" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Type</label>
            <input type="text" name="type" value="{{ old('type') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Reason</label>
            <textarea name="reason" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('reason') }}</textarea>
        </div>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Submit</button>
    </form>
</div>
@endsection
