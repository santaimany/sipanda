<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    JarakController,
    NotificationController,
    Location\LocationController,
    Auth\LoginController,
    Auth\RegisterController,
    Admin\AdminController,
    User\UserController,
    User\PanganController,
    User\PengajuanController,
    User\DashboardKepalaDesaController,
    UserBapanas\ApprovalController,
    UserBapanas\PendataanController,
    UserBapanas\DetailPanganController
};
use App\Http\Controllers\SettingsController;

// Public routes
Route::prefix('register')->group(function () {
    Route::post('/identity', [RegisterController::class, 'registerIdentity']);
    Route::post('/kepaladesa/{userId}', [RegisterController::class, 'registerKepalaDesa']);
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);
Route::post('/calculate-distance', [JarakController::class, 'calculateDistance']);
Route::post('/user/check-status', [UserController::class, 'checkStatus']);

Route::get('/qr-code/{filename}', function ($filename) {
    $path = "/tmp/qrcodes/$filename";

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});

// Location routes (API Emsifa Integration)
Route::prefix('location')->group(function () {
    Route::get('/provinces', [LocationController::class, 'getProvinces']);
    Route::get('/regencies/{province_id}', [LocationController::class, 'getRegencies']);
    Route::get('/districts/{regency_id}', [LocationController::class, 'getDistricts']);
    Route::get('/villages/{district_id}', [LocationController::class, 'getVillages']);
});

// Protected routes (requires token)
Route::middleware(['auth:sanctum'])->group(function () {
    // Pangan routes
    Route::prefix('pangan')->group(function () {
        Route::get('/', [PanganController::class, 'getPanganByUserDesa']);
        Route::get('/persentase', [PanganController::class, 'getPersentaseBeratByDesa']);
        Route::get('/desa-lain', [PanganController::class, 'getPanganDesaLain']);
    });

    // Pengajuan routes
    Route::prefix('pengajuan')->group(function () {
        Route::post('/', [PengajuanController::class, 'create']);
        Route::post('/cek-ketersediaan', [PengajuanController::class, 'cekKetersediaan']);
        Route::post('/simulate-invoice', [PengajuanController::class, 'simulateInvoice']);
        Route::post('/submit', [PengajuanController::class, 'submitPengajuan']);
        Route::get('/{id}/invoice', [PengajuanController::class, 'getInvoice']);
        Route::get('/riwayat', [PengajuanController::class, 'getRiwayatPengajuan']);
        Route::get('/detail/{id}', [PengajuanController::class, 'getPengajuanDetail']);
    });

    // Approval routes (Bapanas)
    Route::prefix('bapanas/pengajuan')->group(function () {
        Route::get('/pending', [ApprovalController::class, 'getPendingPengajuan']);
        Route::put('/{id}/approve', [ApprovalController::class, 'approve']);
        Route::put('/{id}/reject', [ApprovalController::class, 'reject']);
        Route::get('/approved', [ApprovalController::class, 'getApprovedPengajuan']);
        Route::get('/rejected', [ApprovalController::class, 'getRejectedPengajuan']);
    });

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::prefix('kepala_desa')->group(function () {
            Route::get('/user', [DashboardKepalaDesaController::class, 'getUserKades']);
            Route::get('/greet', [DashboardKepalaDesaController::class, 'getGreetingKades']);
            Route::get('/', fn() => response()->json(['message' => 'Welcome to Kepala Desa Dashboard']));
        });
        Route::get('/bapanas', fn() => response()->json(['message' => 'Welcome to Bapanas Dashboard']));
        Route::get('/admin', fn() => response()->json(['message' => 'Welcome to Admin Dashboard']));
    });

    // Pendataan routes
    Route::prefix('pendataan')->group(function () {
        Route::get('/', [PendataanController::class, 'showDesaData']);
        Route::get('/datapangan', [PendataanController::class, 'getDataPangan']);
        Route::post('/insert/{desa_id}', [PendataanController::class, 'insertPanganData']);
        Route::put('/update/{pangan_id}', [PendataanController::class, 'updatePanganData']);
        Route::delete('/delete/{pangan_id}', [PendataanController::class, 'deletePanganData']);
    });

    // Detail Pangan routes
    Route::prefix('detail-pangan')->group(function () {
        Route::get('/', [DetailPanganController::class, 'index']);
        Route::post('/update/{id}', [DetailPanganController::class, 'updateHarga']);
        Route::post('/insert', [DetailPanganController::class, 'insertData']);
        Route::get('/histori/{id}', [DetailPanganController::class, 'historiHarga']);
    });

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::post('/verify-user/{id}/{action}', [AdminController::class, 'verifyUser']);
        Route::get('/pending-users', [AdminController::class, 'getPendingUsers']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'getUserNotifications']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    Route::get('/user/settings', [SettingsController::class, 'getSettings']);
    Route::post('/user/settings/update', [SettingsController::class, 'updateSettings']);
    Route::get('/bapanas/user', [ApprovalController::class, 'getBapanasUser']);


    // Logout
    Route::post('/logout', [LoginController::class, 'logout']);
});

