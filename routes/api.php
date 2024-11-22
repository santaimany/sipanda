<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Location\LocationController;
use App\Http\Controllers\UserBapanas\PendataanController;

// Public routes
Route::post('/register/identity', [RegisterController::class, 'registerIdentity']);
Route::post('/register/kepaladesa/{userId}', [RegisterController::class, 'registerKepalaDesa']);


Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/user/verify-qr-code', [UserController::class, 'verifyQrCode']);

// Rute yang dilindungi
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/admin/verify-user/{id}/{action}', [AdminController::class, 'verifyUser']);
 
    //punya kepala desa
    Route::get('/dashboard/kepala_desa', function () {
        return response()->json(['message' => 'Welcome to Kepala Desa Dashboard']);
    });

    //punya bapanas
    Route::get('/dashboard/bapanas', function () {
        return response()->json(['message' => 'Welcome to Bapanas Dashboard']);
    });
    Route::get('/pendataan', [PendataanController::class, 'showDesaData']);
    Route::post('/pendataan/insert/{desa_id}', [PendataanController::class, 'insertPanganData']);
    Route::put('/pendataan/update/{pangan_id}', [PendataanController::class, 'updatePanganData']); // Update data
    Route::delete('/pendataan/delete/{pangan_id}', [PendataanController::class, 'deletePanganData']); // Delete data

    //punya admin
    Route::get('/dashboard/admin', function () {
        return response()->json(['message' => 'Welcome to Admin Dashboard']);
    });
});

Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/regencies/{province_id}', [LocationController::class, 'getRegencies']);
Route::get('/districts/{regency_id}', [LocationController::class, 'getDistricts']);
Route::get('/villages/{district_id}', [LocationController::class, 'getVillages']);


 

    