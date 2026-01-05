@extends('layouts.app')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold">Leave Requests - {{ $company->name }}</h2>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="pb-2">User</th>
                <th class="pb-2">Dates</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Reason</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaveRequests as $leave)
                <tr class="border-t border-slate-100">
                    <td class="py-2">{{ $leave->user?->name ?? $leave->user_id }}</td>
                    <td class="py-2">{{ $leave->start_date }} - {{ $leave->end_date }}</td>
                    <td class="py-2"><span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-xs">{{ $leave->status }}</span></td>
                    <td class="py-2">{{ $leave->reason ?? '-' }}</td>
                    <td class="py-2">
                        @if ($leave->status === 'pending')
                            <form method="POST" action="{{ route('company.leave_requests.approve', [$company, $leave]) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white" type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('company.leave_requests.reject', [$company, $leave]) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white" type="submit">Reject</button>
                            </form>
                        @else
                            <span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-xs">{{ $leave->status }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $leaveRequests->links() }}</div>
</div>
@endsection
