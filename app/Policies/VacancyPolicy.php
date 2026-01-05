<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;
use App\Policies\Concerns\HandlesTenantAuthorization;

class VacancyPolicy
{
    use HandlesTenantAuthorization;

    public function view(User $user, Vacancy $vacancy): bool
    {
        return $this->sameCompany($user, $vacancy);
    }

    public function create(User $user, Company $company): bool
    {
        return $user->hasRole('Company Admin') && $user->company_id === $company->id;
    }

    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->hasRole('Company Admin') && $this->sameCompany($user, $vacancy);
    }

    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $user->hasRole('Company Admin') && $this->sameCompany($user, $vacancy);
    }
}
