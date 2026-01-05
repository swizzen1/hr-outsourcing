<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Policies\Concerns\HandlesTenantAuthorization;

class CompanyPolicy
{
    use HandlesTenantAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdminOrHr();
    }

    public function view(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasRole('Company Admin') && $user->company_id === $company->id;
    }
}
