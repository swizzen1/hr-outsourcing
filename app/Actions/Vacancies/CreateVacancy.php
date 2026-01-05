<?php

namespace App\Actions\Vacancies;

use App\Models\Company;
use App\Models\Vacancy;

class CreateVacancy
{
    /** @param array<string, mixed> $data */
    public function handle(Company $company, array $data): Vacancy
    {
        $data['company_id'] = $company->id;

        if ($data['status'] === 'published') {
            if (empty($data['published_at'])) {
                $data['published_at'] = now();
            }
        } else {
            $data['published_at'] = null;
        }

        return Vacancy::create($data);
    }
}
