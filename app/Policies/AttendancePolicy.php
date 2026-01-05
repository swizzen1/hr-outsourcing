<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class AttendancePolicy
{
    use HandlesTenantAuthorization;

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('Company Admin')) {
            return $this->sameCompany($user, $attendance);
        }

        return $user->id === $attendance->user_id && $this->sameCompany($user, $attendance);
    }

    public function create(User $user): bool
    {
        return $user->company_id !== null;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id && $this->sameCompany($user, $attendance);
    }
}
