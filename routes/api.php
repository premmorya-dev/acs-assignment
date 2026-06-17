<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::put('/customer/{customer}/payment-status', [CustomerController::class, 'updatePaymentStatus']);
    Route::post('/customer/{customer}/send-notification', [NotificationController::class, 'send']);
    Route::get('/reports/summary', [ReportController::class, 'summary']);

    Route::middleware('admin')->group(function () {  
        Route::post('/admin/upload-csv', [CustomerController::class, 'uploadCsv']);
    });
});
