<?php

namespace App\Actions\LeaveRequests;

use App\Actions\Support\ActionException;
use App\Models\LeaveRequest;
use App\Models\User;

class RejectLeaveRequest
{
    public function handle(User $actor, LeaveRequest $leaveRequest): LeaveRequest
    {
        if ($leaveRequest->status !== 'pending') {
            throw new ActionException('Leave request is not pending', 'status');
        }

        $leaveRequest->status = 'rejected';
        $leaveRequest->decided_by = $actor->id;
        $leaveRequest->decided_at = now();
        $leaveRequest->save();

        return $leaveRequest;
    }
}
