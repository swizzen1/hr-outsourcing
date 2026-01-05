<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hr-sourcing.test'],
            ['name' => 'Global Admin', 'password' => Hash::make('password')]
        );
        $admin->assignRole('Admin');

        $hr = User::firstOrCreate(
            ['email' => 'hr@hr-sourcing.test'],
            ['name' => 'Global HR', 'password' => Hash::make('password')]
        );
        $hr->assignRole('HR');

        Company::all()->each(function (Company $company) {
            $companyAdmin = User::firstOrCreate(
                ['email' => strtolower($company->slug).'_admin@hr-sourcing.test'],
                [
                    'name' => $company->name.' Admin',
                    'company_id' => $company->id,
                    'password' => Hash::make('password'),
                ]
            );
            $companyAdmin->assignRole('Company Admin');

            for ($i = 1; $i <= 2; $i++) {
                $employee = User::firstOrCreate(
                    ['email' => strtolower($company->slug)."_employee{$i}@hr-sourcing.test"],
                    [
                        'name' => $company->name." Employee {$i}",
                        'company_id' => $company->id,
                        'password' => Hash::make('password'),
                        'manager_id' => $companyAdmin->id,
                    ]
                );
                $employee->assignRole('Employee');
            }
        });
    }
}
