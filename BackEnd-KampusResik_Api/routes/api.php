<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PetugasController;

// 1. PUBLIK (Bebas)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/kategori', [PublicController::class, 'getKategori']);
Route::post('/pengaduan', [PublicController::class, 'kirimPengaduan']);
Route::get('/statistik', [PublicController::class, 'getStatistik']);
Route::get('/lacak-laporan', [PublicController::class, 'lacakLaporan']);

// 2. TERPROTEKSI (Wajib Login/Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- GRUP ADMIN ---
    Route::middleware('role:admin')->group(function () {
        // CRUD Kategori
        Route::get('/admin/kategori', [AdminController::class, 'getKategori']);
        Route::get('/admin/kategori/{id}', [AdminController::class, 'showKategori']);
        Route::post('/admin/kategori', [AdminController::class, 'createKategori']);
        Route::put('/admin/kategori/{id}', [AdminController::class, 'updateKategori']);
        Route::delete('/admin/kategori/{id}', [AdminController::class, 'deleteKategori']);

        // CRUD Laporan
        Route::get('/admin/laporan', [AdminController::class, 'getLaporan']);
        Route::get('/admin/laporan/{id}', [AdminController::class, 'showLaporan']);
        Route::put('/admin/laporan/{id}', [AdminController::class, 'updateLaporan']);
        Route::delete('/admin/laporan/{id}', [AdminController::class, 'deleteLaporan']);

        // CRUD User
        Route::get('/admin/users', [AdminController::class, 'getUsers']);
        Route::get('/admin/users/{id}', [AdminController::class, 'showUser']);
        Route::post('/admin/users', [AdminController::class, 'createUser']);
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    });

    // --- GRUP PETUGAS ---
    Route::middleware('role:petugas')->group(function () {
        Route::get('/petugas/tugas', [PetugasController::class, 'getTugas']);
        Route::post('/petugas/update-status/{id}', [PetugasController::class, 'updateStatus']);
    });
});