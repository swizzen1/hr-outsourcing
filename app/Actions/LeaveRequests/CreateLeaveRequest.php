<?php

namespace App\Actions\LeaveRequests;

use App\Actions\Support\ActionException;
use App\Models\LeaveRequest;
use App\Models\User;

class CreateLeaveRequest
{
    /** @param array<string, mixed> $data */
    public function handle(User $user, array $data): LeaveRequest
    {
        if ($user->company_id === null) {
            throw new ActionException('User must belong to a company', 'company_id');
        }

        return LeaveRequest::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'type' => $data['type'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'decided_by' => null,
            'decided_at' => null,
        ]);
    }
}
