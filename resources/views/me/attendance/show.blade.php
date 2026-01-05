@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold">My Attendance</h2>
    <p class="mt-2 text-sm text-slate-600">Date: {{ now()->toDateString() }}</p>
    <div class="mt-3 text-sm">
        <p>Check-in: <strong>{{ $attendance?->check_in_at?->toTimeString() ?? 'Not checked in' }}</strong></p>
        <p>Check-out: <strong>{{ $attendance?->check_out_at?->toTimeString() ?? 'Not checked out' }}</strong></p>
    </div>

    <div class="mt-4 flex gap-3">
        <form method="POST" action="{{ route('me.attendance.checkin') }}">
            @csrf
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Check In</button>
        </form>
        <form method="POST" action="{{ route('me.attendance.checkout') }}">
            @csrf
            <button class="rounded-lg bg-slate-600 px-4 py-2 text-sm font-semibold text-white" type="submit">Check Out</button>
        </form>
    </div>
</div>
@endsection
