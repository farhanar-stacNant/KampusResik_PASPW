<?php

use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

// Public API Routes (pake web.php dulu)
Route::prefix('api/public')->group(function () {
    Route::get('/statistics', [PublicController::class, 'statistics']);
    Route::get('/waste-distribution', [PublicController::class, 'wasteDistribution']);
    Route::get('/waste-habits', [PublicController::class, 'wasteHabits']);
    Route::get('/reports', [PublicController::class, 'reports']);
    Route::post('/reports', [PublicController::class, 'storeReport']);
    Route::get('/reports/{id}', [PublicController::class, 'reportDetail']);
    Route::get('/status/{kode}', [PublicController::class, 'checkStatus']);
});