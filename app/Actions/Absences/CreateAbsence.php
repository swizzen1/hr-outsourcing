<?php

namespace App\Actions\Absences;

use App\Actions\Support\ActionException;
use App\Models\Absence;
use App\Models\Company;
use App\Models\User;

class CreateAbsence
{
    /** @param array<string, mixed> $data */
    public function handle(User $actor, Company $company, array $data): Absence
    {
        $user = User::findOrFail($data['user_id']);

        if ($user->company_id !== $company->id) {
            throw new ActionException('User does not belong to this company', 'user_id');
        }

        $date = $data['date'];

        $duplicate = Absence::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->exists();

        if ($duplicate) {
            throw new ActionException('Absence already registered for this date', 'date');
        }

        return Absence::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'date' => $date,
            'reason' => $data['reason'] ?? null,
            'created_by' => $actor->id,
        ]);
    }
}
