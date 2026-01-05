<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = ['Software Engineer', 'HR Specialist', 'Accountant'];

        Company::all()->each(function (Company $company) use ($positions) {
            foreach ($positions as $name) {
                Position::firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $name,
                ]);
            }
        });
    }
}
