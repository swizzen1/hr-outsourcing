<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VacancySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        Company::all()->each(function (Company $company) use ($now) {
            Vacancy::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'title' => $company->name.' Backend Developer',
                ],
                [
                    'description' => 'Build and maintain APIs for internal platforms.',
                    'location' => 'Remote',
                    'employment_type' => 'full_time',
                    'status' => 'draft',
                ]
            );

            Vacancy::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'title' => $company->name.' QA Engineer',
                ],
                [
                    'description' => 'Own test plans and automation for product releases.',
                    'location' => 'Hybrid',
                    'employment_type' => 'contract',
                    'status' => 'published',
                    'published_at' => $now->copy()->subDay(),
                    'expiration_date' => $now->copy()->addDays(30)->toDateString(),
                ]
            );
        });
    }
}
