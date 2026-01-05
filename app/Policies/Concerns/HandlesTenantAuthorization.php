<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HandlesTenantAuthorization
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminOrHr()) {
            return true;
        }

        return null;
    }

    protected function sameCompany(User $user, Model $model): bool
    {
        return $user->company_id !== null && $user->company_id === $model->company_id;
    }
}
