<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

Route::middleware('verify.api.token')->group(function () {
    Route::get('/kategori-sampah', [PublicController::class, 'getKategoriSampah']);
    Route::get('/laporan', [PublicController::class, 'getLaporan']);
    Route::get('/laporan/{id}', [PublicController::class, 'getDetailLaporan']);
    Route::get('/laporan/{id}/timeline', [PublicController::class, 'getTimeline']);
    Route::post('/laporan', [PublicController::class, 'storeLaporan']);
    Route::post('/laporan/{id}/status', [PublicController::class, 'updateStatus']);
    Route::get('/statistik', [PublicController::class, 'getStatistik']);

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/update-profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });

    Route::prefix('admin')->group(function () {
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
        Route::post('/reports/{id}/status', [AdminController::class, 'updateReportStatus']);
        Route::get('/progress', [AdminController::class, 'progress']);
        Route::get('/jadwal', [AdminController::class, 'jadwal']);
        Route::get('/export/excel', [AdminController::class, 'exportExcel']);
        Route::get('/export/pdf', [AdminController::class, 'exportPDF']);
    });
        
    Route::prefix('public')->group(function () {
        Route::get('/statistics', [PublicController::class, 'statistics']);
        Route::get('/waste-distribution', [PublicController::class, 'wasteDistribution']);
        Route::get('/waste-habits', [PublicController::class, 'wasteHabits']);
        Route::get('/reports', [PublicController::class, 'reports']);
        Route::post('/reports', [PublicController::class, 'storeReport'])
            ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
        Route::get('/reports/{id}', [PublicController::class, 'reportDetail']);
        Route::get('/status/{kode}', [PublicController::class, 'checkStatus']);
    });
});
Route::get('/admin/progress-harian', [AdminController::class, 'progressHarian'])->middleware('auth:sanctum');