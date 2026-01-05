<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class LeaveRequestPolicy
{
    use HandlesTenantAuthorization;

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->hasRole('Company Admin')) {
            return $this->sameCompany($user, $leaveRequest);
        }

        return $user->id === $leaveRequest->user_id && $this->sameCompany($user, $leaveRequest);
    }

    public function create(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->id === $leaveRequest->user_id && $this->sameCompany($user, $leaveRequest);
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('Company Admin') && $this->sameCompany($user, $leaveRequest);
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('Company Admin') && $this->sameCompany($user, $leaveRequest);
    }
}
