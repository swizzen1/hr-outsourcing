<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Position;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class PositionPolicy
{
    use HandlesTenantAuthorization;

    public function view(User $user, Position $position): bool
    {
        return $this->sameCompany($user, $position);
    }

    public function create(User $user, Company $company): bool
    {
        return $user->hasRole('Company Admin') && $user->company_id === $company->id;
    }

    public function update(User $user, Position $position): bool
    {
        return $user->hasRole('Company Admin') && $this->sameCompany($user, $position);
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->hasRole('Company Admin') && $this->sameCompany($user, $position);
    }
}
