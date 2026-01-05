<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $user = auth()->user();

        if (!$user instanceof User) {
            return;
        }

        if ($user->isAdminOrHr()) {
            return;
        }

        $builder->where($model->getTable().'.company_id', $user->company_id);
    }
}
