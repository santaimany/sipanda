<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\User\DashboardKepalaDesaController; // Import untuk dashboard kepala desa

// Public routes
<<<<<<< HEAD
Route::post('/register/identity', [RegisterController::class, 'registerIdentity']);
Route::post('/register/kepaladesa/{userId}', [RegisterController::class, 'registerKepalaDesa']);


=======
Route::post('/register', [RegisterController::class, 'register']);
>>>>>>> ee61a7a (get user kepala desa -> done)
Route::post('/login', [LoginController::class, 'login']);

// Protected routes (hanya bisa diakses dengan token)
Route::middleware(['auth:sanctum'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout']);

    // Admin routes
    Route::post('/admin/verify-user/{id}/{action}', [AdminController::class, 'verifyUser']);

    // Dashboard routes
    Route::get('/dashboard/kepala_desa/user', [DashboardKepalaDesaController::class, 'getUserKades']); // Data user dan desa untuk kepala desa
    Route::get('/dashboard/kepala_desa', function () {
        return response()->json(['message' => 'Welcome to Kepala Desa Dashboard']);
    });

    Route::get('/dashboard/bapanas', function () {
        return response()->json(['message' => 'Welcome to Bapanas Dashboard']);
    });

    Route::get('/dashboard/admin', function () {
        return response()->json(['message' => 'Welcome to Admin Dashboard']);
    });

    // QR Code Verification
    Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);
});

// Location routes (API Emsifa Integration)
Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/regencies/{province_id}', [LocationController::class, 'getRegencies']);
Route::get('/districts/{regency_id}', [LocationController::class, 'getDistricts']);
Route::get('/villages/{district_id}', [LocationController::class, 'getVillages']);
