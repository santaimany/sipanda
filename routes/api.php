<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JarakController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\PanganController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\PengajuanController;
use App\Http\Controllers\Bapanas\ApprovalController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\User\DashboardKepalaDesaController; // Import untuk dashboard kepala desa

// Public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::post('/calculate-distance', [JarakController::class, 'calculateDistance']);


// Protected routes (hanya bisa diakses dengan token)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/pangan', [PanganController::class, 'getPanganByUserDesa']);
    Route::get('/pangan/persentase', [PanganController::class, 'getPersentaseBeratByDesa']);
    Route::get('/pangan/desa-lain', [PanganController::class, 'getPanganDesaLain']);

    Route::post('/pengajuan', [PengajuanController::class, 'create']);
            
    // Mendapatkan invoice pengajuan berdasarkan ID
    Route::get('/pengajuan/{id}/invoice', [PengajuanController::class, 'getInvoice']);

  
    Route::get('/pengajuan/history', [PengajuanController::class, 'getUserHistory']);
    Route::get('/pengajuan/riwayat/{desaId}', [PengajuanController::class, 'getRiwayatPengajuan']);
    Route::get('/bapanas/pengajuan/pending', [ApprovalController::class, 'getPendingPengajuan']);
    Route::put('/bapanas/pengajuan/{id}/approve', [ApprovalController::class, 'approve']);
    Route::put('/bapanas/pengajuan/{id}/reject', [ApprovalController::class, 'reject']);
    Route::get('/bapanas/pengajuan/approved', [ApprovalController::class, 'getApprovedPengajuan']);
    Route::get('/bapanas/pengajuan/rejected', [ApprovalController::class, 'getRejectedPengajuan']);


    // Logout
    Route::post('/logout', [LoginController::class, 'logout']);

    // Admin routes
    Route::post('/admin/verify-user/{id}/{action}', [AdminController::class, 'verifyUser']);

    // Dashboard routes
    Route::get('/dashboard/kepala_desa/user', [DashboardKepalaDesaController::class, 'getUserKades']); // Data user dan desa untuk kepala desa
    Route::get('/dashboard/kepala_desa/greet', [DashboardKepalaDesaController::class, 'getGreetingKades']); // Greeting untuk kepala desa
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
