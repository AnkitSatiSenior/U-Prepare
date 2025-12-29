<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PhysicalEpcProgressApiController;
use App\Http\Controllers\Api\SocialSafeguardEntryApiController;
use App\Http\Controllers\Api\PhysicalBoqProgressApiController;
use App\Http\Controllers\Api\FinancialProgressUpdateApiController;
use App\Http\Controllers\Api\WorkProgressDataController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth Required)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth.token')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User & Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardApiController::class, 'dashboard']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Package Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index']);               // All packages
        Route::get('/assigned', [PackageController::class, 'assignedPackages']); // User assigned packages
    });

    /*
    |--------------------------------------------------------------------------
    | Financial Progress Updates
    |--------------------------------------------------------------------------
    */
    Route::prefix('financial-progress-updates')->group(function () {
        Route::get('/', [FinancialProgressUpdateApiController::class, 'index']);
        Route::post('/', [FinancialProgressUpdateApiController::class, 'store']);
        Route::match(['put', 'patch'], '/{id}', [FinancialProgressUpdateApiController::class, 'update']);
        Route::delete('/{id}', [FinancialProgressUpdateApiController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Work Progress
    |--------------------------------------------------------------------------
    */
    Route::prefix('work-progress')->group(function () {
        Route::get('/', [WorkProgressDataController::class, 'index']);
        Route::get('/create', [WorkProgressDataController::class, 'create']);
        Route::post('/store', [WorkProgressDataController::class, 'store']);
        Route::get('/{id}', [WorkProgressDataController::class, 'show']);
        Route::post('/upload-images', [WorkProgressDataController::class, 'uploadImagesToLastProgress']);
        Route::delete('/{id}', [WorkProgressDataController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | BOQ Progress
    |--------------------------------------------------------------------------
    */
    Route::prefix('boq-progress')->group(function () {
        Route::get('/', [PhysicalBoqProgressApiController::class, 'index']);
        Route::get('/entries', [PhysicalBoqProgressApiController::class, 'indexWithEntries']);
        Route::post('/', [PhysicalBoqProgressApiController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | EPC Progress
    |--------------------------------------------------------------------------
    */
    Route::prefix('epc-progress')->group(function () {
        Route::get('/', [PhysicalEpcProgressApiController::class, 'index']);
        Route::post('/', [PhysicalEpcProgressApiController::class, 'store']);
        Route::get('/entries', [PhysicalEpcProgressApiController::class, 'indexWithEntries']);
    });

    /*
    |--------------------------------------------------------------------------
    | Social Safeguard
    |--------------------------------------------------------------------------
    */
    Route::prefix('social-safeguard')->group(function () {

        Route::get('/entries/{project_id}/{compliance_id}/{phase_id?}', 
            [SocialSafeguardEntryApiController::class, 'index']);

        Route::get('/entry/{id}', 
            [SocialSafeguardEntryApiController::class, 'show']);

        Route::post('/entry/upload', 
            [SocialSafeguardEntryApiController::class, 'upload']);

        Route::post('/entry/save', 
            [SocialSafeguardEntryApiController::class, 'save']);

        Route::put('/entry/{id}', 
            [SocialSafeguardEntryApiController::class, 'update']);

        Route::delete('/entry/{id}', 
            [SocialSafeguardEntryApiController::class, 'destroy']);

        Route::get('/overview', 
            [SocialSafeguardEntryApiController::class, 'overview']);

    });

});
