<?php

use App\Http\Controllers\Api\CompanyVacancyController;
use App\Http\Controllers\Api\CompanyAbsenceController;
use App\Http\Controllers\Api\CompanyLeaveRequestApprovalController;
use App\Http\Controllers\Api\MeLeaveRequestController;
use App\Http\Controllers\Api\MeAttendanceController;
use App\Http\Controllers\Api\PublicVacancyController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Models\Company;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('public')->group(function () {
        Route::get('vacancies', [PublicVacancyController::class, 'index']);
        Route::get('vacancies/{id}', [PublicVacancyController::class, 'show']);
    });

    Route::post('auth/login', [ApiAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'company.access'])->group(function () {
        Route::get('companies/{company}/ping', function (Company $company) {
            return response()->json([
                'company_id' => $company->id,
                'status' => 'ok',
            ]);
        });

        Route::post('companies/{company}/vacancies', [CompanyVacancyController::class, 'store']);
    });
});
