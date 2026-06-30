<?php

use App\Http\Controllers\Api\{
    ActivityLogController , ActivityTypeController , AddressController , AttachmentController,
    CharitableCompanyController , CompanyController , DepartmentController ,
    DistrictController , FileController, FileStatusController , JobTypeController ,
    TaxCollectorController , TaxPayerController , UserController , AuthController,
    FileMovementController , NotificationController, PaymentTypeController ,
    RegionController , RequestController , ResetPasswordController, RecyclePinController,
    StatisticsController , TaxInformationController , TaxPayerMobileController ,
    TaxTypeController
};

use App\Http\Middleware\appUsersMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Facades\ResponseCache;

Route::post('login' , [AuthController::class, 'login'])->name('auth.login');
Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('auth.forget-password');

Route::post('request' , [ResetPasswordController::class , 'request'])->name('request');
Route::post('verify' , [ResetPasswordController::class , 'verify'])->name('verify');
Route::post('reset' , [ResetPasswordController::class , 'reset'])->name('reset');
Route::post('resend' , [ResetPasswordController::class , 'resend'])->name('resend');

Route::post('create-tax-payer-mobile', [TaxPayerMobileController::class, 'store']);
Route::post('tax-payer-mobile-login', [TaxPayerMobileController::class, 'TaxPayerMobileLogin']);

Route::middleware(['auth:sanctum' , 'cacheResponse'])->group(function () {

    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    Route::post('complete-profile', [AuthController::class, 'completeProfile'])->name('auth.complete-profile');
    Route::post('logout' , [AuthController::class , 'logout'])->name('auth.logout');
    Route::get('get_user/{id}' , [UserController::class , 'show'])->name('users.show');
    Route::get('activity-log' , [ActivityLogController::class , 'index'])->name('activity-log.index');

});

Route::middleware(['auth:sanctum' , appUsersMiddleware::class , 'cacheResponse'])->group(function () {

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
    Route::get('some-sections-statistics', [StatisticsController::class, 'getSomeSectionsStatistics'])->name('statistics.getSomeSectionsStatistics');

    Route::apiResource('tax-collectors', TaxCollectorController::class);

    Route::apiResource('tax-payers', TaxPayerController::class);
    Route::get('tax-payer-by-userId/{id}', [TaxPayerController::class , 'findTaxPayerByUserID'])->name('tax-payer-by-userId');
    Route::get('get-tax-payers-with-special-info', [TaxPayerController::class , 'getTaxPayersWithSpecialInfo'])->name('get-tax-payers-with-special-info');
    Route::get('get-tax-payers-with-source', [TaxPayerController::class , 'getAllTaxPayersWithSource'])->name('get-tax-payers-with-source');
    Route::post('tax-payers/create-file-to-existing', [TaxPayerController::class, 'createFileToExistingTaxPayer'])->name('tax-payers.create-file-to-existing');

    Route::apiResource('companies', CompanyController::class);
    Route::post('companies/create-file-to-existing', [CompanyController::class, 'createCompanyFileToExistingTaxPayer'])->name('companies.create-file-to-existing');

    Route::apiResource('charitable-companies', CharitableCompanyController::class);
    Route::post('charitable-companies/create-file-to-existing', [CharitableCompanyController::class, 'createCharitableCompanyFileToExistingTaxPayer'])
        ->name('charitable-companies.create-file-to-existing');

    Route::apiResource('tax-types', TaxTypeController::class);

    Route::apiResource('tax-informations', TaxInformationController::class);

    Route::apiResource('file-status' , FileStatusController::class);

    Route::get('/files/report', [FileController::class, 'generateBulkFilesReport'])->name('files.bulk-report');
    Route::apiResource('/files', FileController::class);
    Route::get('/files/{id}/report', [FileController::class, 'generateReport'])->name('files.report');

    Route::apiResource('/files-movements', FileMovementController::class);
    Route::get('/files-movements-report', [FileMovementController::class, 'generateReport'])->name('files-movements.report');

    Route::apiResource('/requests', RequestController::class);

    Route::get('/get-pending-requests', [RequestController::class , 'getPendingRequests'])
    ->name('get-pending-requests');

    Route::put('/accept-request', [RequestController::class , 'acceptRequest'])
    ->name('accept-request');
    Route::get('/get-confirmed-requests', [RequestController::class , 'getConfirmedRequests'])
    ->name('get-confirmed-requests');

    Route::post('/archive-request', [RequestController::class , 'storeArchivedRequestToFilesTable'])
    ->name('archive-request');
    Route::get('/get-archived-requests', [RequestController::class , 'getArchivedRequests'])
    ->name('get-archived-requests');

    Route::put('/reject-request', [RequestController::class , 'rejectRequest'])
    ->name('reject-request');
    Route::get('/get-rejected-requests', [RequestController::class , 'getRejectedRequests'])
    ->name('get-rejected-requests');

    Route::get('/exists-request' , [RequestController::class , 'existsRequest'])
    ->name('exists-request');

    Route::apiResource('attachment' , AttachmentController::class);

    Route::apiResource('notification' , NotificationController::class);

    Route::get('recycle-pin', [RecyclePinController::class, 'index'])->name('recycle-pin.index');
    Route::get('recycle-pin/{id}', [RecyclePinController::class, 'show'])->name('recycle-pin.show');
    Route::delete('recycle-pin/{id}', [RecyclePinController::class, 'destroy'])->name('recycle-pin.destroy');
    Route::post('recycle-pin/{id}/restore', [RecyclePinController::class, 'restore'])->name('recycle-pin.restore');

    // Mobile App Routes
    Route::put('update-tax-payer-mobile', [TaxPayerMobileController::class, 'update'])
    ->name('update-tax-payer-mobile');

    Route::post('tax-payer-mobile-logout' , [TaxPayerMobileController::class , 'TaxPayerMobileLogout'])
    ->name('tax-payer-mobile-logout');

    Route::get('get-tax-payer-mobile-profile' , [TaxPayerMobileController::class , 'show'])
    ->name('get-tax-payer-mobile-profile');

    Route::get('get-tax-payer-mobile-files' , [TaxPayerMobileController::class , 'index'])
    ->name('get-tax-payer-mobile-files');

    Route::get('get-tax-payer-mobile-file_By_Id/{id}' , [TaxPayerMobileController::class , 'getTaxPayerFileById'])
    ->name('get-tax-payer-mobile-file_By_Id');

});

// This route for check if the  queue is working or not
Route::get('/run-queue-now', function() {
    Artisan::call('queue:work --stop-when-empty');
    return response()->json([
        'output' => Artisan::output()
    ]);
});


Route::get('/clear-config', function () {
    Artisan::call('config:clear');
    ResponseCache::clear();
    return response()->json(['message' => 'Config cache cleared successfully!']);
});
