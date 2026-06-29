<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

// ========== PUBLIC API ==========
Route::get('/kategori-sampah', [PublicController::class, 'getKategoriSampah']);
Route::post('/laporan-sampah', [PublicController::class, 'storeLaporan']);
Route::get('/laporan-sampah/{kode_laporan}', [PublicController::class, 'getLaporanByKode']);
Route::get('/laporan-sampah/status/{status}', [PublicController::class, 'getLaporanByStatus']);
Route::get('/laporan-sampah', [PublicController::class, 'getLaporan']);
Route::get('/statistik', [PublicController::class, 'getStatistik']);

// ========== AUTH API ==========
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/auth/update-profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

// ========== PETUGAS API ==========
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/public/reports', [PublicController::class, 'getLaporan']);
    Route::get('/public/reports/{id}', [PublicController::class, 'getLaporanById']);
    Route::post('/laporan/{id}/status', [PublicController::class, 'updateLaporanStatus']);
    Route::get('/me', [PublicController::class, 'getProfile']);
    Route::put('/me', [PublicController::class, 'updateProfilePetugas']);
});

// ========== ADMIN & KOORDINATOR API ==========
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/statistics', [AdminController::class, 'statistics']);
    Route::get('/weekly-chart', [AdminController::class, 'weeklyChart']);
    Route::get('/popular-categories', [AdminController::class, 'popularCategories']);
    Route::get('/categories', [AdminController::class, 'categories']);
    Route::get('/categories/{id}', [AdminController::class, 'showCategory']);
    Route::post('/categories', [AdminController::class, 'storeCategory']);
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);
    Route::get('/petugas', [AdminController::class, 'petugas']);
    Route::post('/petugas', [AdminController::class, 'storePetugas']);
    Route::get('/reports', [AdminController::class, 'reports']);
    Route::post('/reports', [AdminController::class, 'storeLaporan']);
    Route::put('/reports/{id}', [AdminController::class, 'updateLaporan']);
    Route::delete('/reports/{id}', [AdminController::class, 'deleteLaporan']);
    Route::post('/reports/{id}/status', [AdminController::class, 'updateReportStatus']);
    Route::get('/progress', [AdminController::class, 'progress']);
    Route::get('/jadwal', [AdminController::class, 'jadwal']);
    Route::post('/jadwal', [AdminController::class, 'storeJadwal']);
    Route::get('/progress-harian', [AdminController::class, 'progressHarian']);
    Route::get('/export/excel', [AdminController::class, 'exportExcel']);
    Route::get('/export/pdf', [AdminController::class, 'exportPDF']);
    Route::get('/rekap', [AdminController::class, 'rekap']);
});
