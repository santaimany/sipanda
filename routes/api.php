<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Location\LocationController;

// Public routes
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Rute yang dilindungi
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admin/verify-user/{id}/{action}', [AdminController::class, 'verifyUser']);
    Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);

    Route::get('/dashboard/kepala_desa', function () {
        return response()->json(['message' => 'Welcome to Kepala Desa Dashboard']);
    });

    Route::get('/dashboard/bapanas', function () {
        return response()->json(['message' => 'Welcome to Bapanas Dashboard']);
    });

    Route::get('/dashboard/admin', function () {
        return response()->json(['message' => 'Welcome to Admin Dashboard']);
    });
});

Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/regencies/{province_id}', [LocationController::class, 'getRegencies']);
Route::get('/districts/{regency_id}', [LocationController::class, 'getDistricts']);
Route::get('/villages/{district_id}', [LocationController::class, 'getVillages']);


 

    