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
use App\Http\Controllers\UserBapanas\ApprovalController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\UserBapanas\PendataanController;
use App\Http\Controllers\UserBapanas\DetailPanganController;
use App\Http\Controllers\User\DashboardKepalaDesaController; // Import untuk dashboard kepala desa

// Public routes
Route::post('/register/identity', [RegisterController::class, 'registerIdentity']);
Route::post('/register/kepaladesa/{userId}', [RegisterController::class , 'registerKepalaDesa'] );
Route::post('/login', [LoginController::class, 'login']);

// QR Code Verification
Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);

Route::post('/calculate-distance', [JarakController::class, 'calculateDistance']);


// Protected routes (hanya bisa diakses dengan token)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/pangan', [PanganController::class, 'getPanganByUserDesa']);
    Route::get('/pangan/persentase', [PanganController::class, 'getPersentaseBeratByDesa']);
    Route::get('/pangan/desa-lain', [PanganController::class, 'getPanganDesaLain']);

    Route::post('/pengajuan', [PengajuanController::class, 'create']);
    Route::post('/pengajuan/cek-ketersediaan', [PengajuanController::class, 'cekKetersediaan']);
    Route::post('/pengajuan/simulate-invoice', [PengajuanController::class, 'simulateInvoice']);
    Route::post('/pengajuan/submit', [PengajuanController::class, 'submitPengajuan']);

            
    // Mendapatkan invoice pengajuan berdasarkan ID
    Route::get('/pengajuan/{id}/invoice', [PengajuanController::class, 'getInvoice']);

  
    
    Route::get('/pengajuan/riwayat', [PengajuanController::class, 'getRiwayatPengajuan']);
    Route::get('/pengajuan/detail/{id}', [PengajuanController::class, 'getPengajuanDetail']);

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
    Route::get('/pendataan', [PendataanController::class, 'showDesaData']);
    Route::get('/pendataan/datapangan', [PendataanController::class, 'getDataPangan']);
    Route::post('/pendataan/insert/{desa_id}', [PendataanController::class, 'insertPanganData']);
    Route::put('/pendataan/update/{pangan_id}', [PendataanController::class, 'updatePanganData']); // Update data
    Route::delete('/pendataan/delete/{pangan_id}', [PendataanController::class, 'deletePanganData']); // Delete data

    Route::get('/detail-pangan', [DetailPanganController::class, 'index']);
    Route::post('/detail-pangan/update/{id}', [DetailPanganController::class, 'updateHarga']);
    Route::post('/detail-pangan/insert', [DetailPanganController::class, 'insertData']);
    Route::get('/detail-pangan/histori/{id}', [DetailPanganController::class, 'historiHarga']);

    Route::get('/dashboard/admin', function () {
        return response()->json(['message' => 'Welcome to Admin Dashboard']);
    });
<<<<<<< HEAD
});

// Location routes (API Wilayah Indonesia)
=======

});

// QR Code Verification
Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);
// Location routes (API Emsifa Integration)
>>>>>>> 6d4ad16c1c184ef905aa2365180d8b521701ec9e
Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/regencies/{province_id}', [LocationController::class, 'getRegencies']);
Route::get('/districts/{regency_id}', [LocationController::class, 'getDistricts']);
Route::get('/villages/{district_id}', [LocationController::class, 'getVillages']);
