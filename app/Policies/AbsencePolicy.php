<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\Company;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class AbsencePolicy
{
    use HandlesTenantAuthorization;

    public function view(User $user, Absence $absence): bool
    {
        if ($user->hasRole('Company Admin')) {
            return $this->sameCompany($user, $absence);
        }

        return $user->id === $absence->user_id && $this->sameCompany($user, $absence);
    }

    public function create(User $user, Company $company): bool
    {
        return $user->hasRole('Company Admin') && $user->company_id === $company->id;
    }
}
