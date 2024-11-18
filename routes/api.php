<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Location\LocationController;

// Public routes
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/regencies/{province_id}', [LocationController::class, 'getRegencies']);
Route::get('/districts/{regency_id}', [LocationController::class, 'getDistricts']);
Route::get('/villages/{district_id}', [LocationController::class, 'getVillages']);


 

    Route::post('/admin/verify-user/{id}/{action}', [AdminController::class, 'verifyUser']);
    Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);