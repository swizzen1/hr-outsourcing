<?php

namespace App\Actions\Attendance;

use App\Actions\Support\ActionException;
use App\Models\Attendance;
use App\Models\User;

class CheckOut
{
    public function handle(User $user): Attendance
    {
        if ($user->company_id === null) {
            throw new ActionException('User must belong to a company', 'company_id');
        }

        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_at) {
            throw new ActionException('Must check in before checking out', 'attendance');
        }

        if ($attendance->check_out_at) {
            throw new ActionException('Already checked out today', 'attendance');
        }

        $attendance->check_out_at = now();
        $attendance->save();

        return $attendance;
    }
}
