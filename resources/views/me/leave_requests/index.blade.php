@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">My Leave Requests</h2>
        <a class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('me.leave_requests.create') }}">New Leave Request</a>
    </div>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="pb-2">Dates</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Type</th>
                <th class="pb-2">Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaveRequests as $leave)
                <tr class="border-t border-slate-100">
                    <td class="py-2">{{ $leave->start_date }} - {{ $leave->end_date }}</td>
                    <td class="py-2"><span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-xs">{{ $leave->status }}</span></td>
                    <td class="py-2">{{ $leave->type ?? '-' }}</td>
                    <td class="py-2">{{ $leave->reason ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $leaveRequests->links() }}</div>
</div>
@endsection
