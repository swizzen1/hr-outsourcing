<?php

namespace App\Actions\Attendance;

use App\Actions\Support\ActionException;
use App\Models\Attendance;
use App\Models\User;

class CheckIn
{
    /**
     * @return array{attendance: Attendance, created: bool}
     */
    public function handle(User $user): array
    {
        if ($user->company_id === null) {
            throw new ActionException('User must belong to a company', 'company_id');
        }

        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            if ($attendance->check_in_at) {
                throw new ActionException('Already checked in today', 'attendance');
            }

            $attendance->check_in_at = now();
            $attendance->save();

            return ['attendance' => $attendance, 'created' => false];
        }

        $attendance = Attendance::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'date' => $today,
            'check_in_at' => now(),
        ]);

        return ['attendance' => $attendance, 'created' => true];
    }
}
