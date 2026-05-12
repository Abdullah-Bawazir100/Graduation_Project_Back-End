<?php

use App\Http\Controllers\Api\{
    ActivityLogController , ActivityTypeController , AddressController ,
    CharitableCompanyController , CompanyController , DepartmentController ,
    DistrictController , JobTypeController , TaxCollectorController ,
    TaxPayerController , UserController , AuthController,
    PaymentTypeController , RegionController , StatisticsController,
    TaxInformationController , TaxPayerMobileController , TaxTypeController
};

use App\Http\Middleware\appUsersMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('login' , [AuthController::class, 'login'])->name('auth.login');
Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('auth.forget-password');

Route::post('create-tax-payer-mobile', [TaxPayerMobileController::class, 'store']);
Route::post('tax-payer-mobile-login', [TaxPayerMobileController::class, 'TaxPayerMobileLogin']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    Route::post('complete-profile', [AuthController::class, 'completeProfile'])->name('auth.complete-profile');
    Route::post('logout' , [AuthController::class , 'logout'])->name('auth.logout');
    Route::get('get_user/{id}' , [UserController::class , 'show'])->name('users.show');
    Route::get('activity-log' , [ActivityLogController::class , 'index'])->name('activity-log.index');

});

Route::middleware(['auth:sanctum' , appUsersMiddleware::class])->group(function () {

    Route::post('create-user' , [AuthController::class, 'createUser'])->name('auth.create-user');
    Route::apiResource('app_users', UserController::class);

    Route::apiResource('departments', DepartmentController::class);
    Route::post('departments/{id}/move-users', [DepartmentController::class, 'moveUsersToAnotherDepartment'])->name('departments.move-users');

    Route::apiResource('activity_types' , ActivityTypeController::class);

    Route::apiResource('payment_types' , PaymentTypeController::class);

    Route::apiResource('regions' , RegionController::class);

    Route::apiResource('districts' , DistrictController::class);
    Route::get('districts/region/{regionId}', [DistrictController::class, 'getByRegion'])->name('districts.getByRegion');

    Route::apiResource('addresses' , AddressController::class);

    Route::apiResource('job-types' , JobTypeController::class);
    Route::post('job-types/{id}/move-TaxCollectors', [JobTypeController::class, 'moveTaxCollectorsToAnotherJobType'])->name('job-types.move-TaxCollectors');

    Route::get('statistics', [StatisticsController::class, 'getStatistics'])->name('statistics.getStatistics');

    Route::apiResource('tax-collectors', TaxCollectorController::class);

    Route::apiResource('tax-payers', TaxPayerController::class);
    Route::get('tax-payer-by-userId/{id}', [TaxPayerController::class , 'findTaxPayerByUserID'])->name('tax-payer-by-userId');
    Route::get('get-tax-payers-with-special-info', [TaxPayerController::class , 'getTaxPayersWithSpecialInfo'])->name('get-tax-payers-with-special-info');
    Route::post('tax-payers/create-file-to-existing', [TaxPayerController::class, 'createFileToExistingTaxPayer'])->name('tax-payers.create-file-to-existing');

    Route::apiResource('companies', CompanyController::class);
    Route::post('companies/create-file-to-existing', [CompanyController::class, 'createCompanyFileToExistingTaxPayer'])->name('companies.create-file-to-existing');

    Route::apiResource('charitable-companies', CharitableCompanyController::class);
    Route::post('charitable-companies/create-file-to-existing', [CharitableCompanyController::class, 'createCharitableCompanyFileToExistingTaxPayer'])
        ->name('charitable-companies.create-file-to-existing');

    Route::apiResource('tax-types', TaxTypeController::class);

    Route::apiResource('tax-informations', TaxInformationController::class);

    // Mobile App Routes
    Route::put('update-tax-payer-mobile', [TaxPayerMobileController::class, 'update']);
    Route::post('tax-payer-mobile-logout' , [TaxPayerMobileController::class , 'TaxPayerMobileLogout']);
    Route::get('get-tax-payer-mobile-profile' , [TaxPayerMobileController::class , 'show']);

});
