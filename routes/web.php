<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/penilaian', [AssessmentController::class, 'showToday'])->name('penilaian.show');
    Route::get('/penilaian/{date}', [AssessmentController::class, 'show'])->name('penilaian.details');
    Route::post('/penilaian/{date}', [AssessmentController::class, 'save'])->name('penilaian.save');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
