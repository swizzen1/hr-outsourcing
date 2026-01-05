@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold">Employee Dashboard</h2>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="text-lg font-semibold">Attendance (Today)</h3>
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

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">My Leave Requests</h3>
        <a class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('me.leave_requests.create') }}">New Leave Request</a>
    </div>
    <table class="mt-4 w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="pb-2">Dates</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaveRequests as $leave)
                <tr class="border-t border-slate-100">
                    <td class="py-2">{{ $leave->start_date }} - {{ $leave->end_date }}</td>
                    <td class="py-2"><span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-xs">{{ $leave->status }}</span></td>
                    <td class="py-2">{{ $leave->reason ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
