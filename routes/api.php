<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ApprovalController;

Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('/admin/approval/{id}', [ApprovalController::class, 'updateStatus']);
});