<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\WorklogController;
  
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//Authentication and Authorization APIs
Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);

//Manage Projects and Worklogs
Route::prefix('admin')->middleware('auth:sanctum')->group( function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('work-logs', WorklogController::class);
});

//Record login, logout and upload records as bulk
Route::prefix('work-logs')->group( function () {
    Route::post('login', [WorklogController::class, 'login']);
    Route::post('logout', [WorklogController::class, 'logout']);
    Route::post('bulk-upload', [WorklogController::class, 'bulkUpload']);
});

//Show reports
Route::prefix('reports')->group( function () {
    Route::prefix('projects')->group( function () {
        Route::post('billable-hours', [WorklogController::class, 'billableHours']);
        Route::post('getpeak-time', [WorklogController::class, 'getPeakTime']);
    });
});