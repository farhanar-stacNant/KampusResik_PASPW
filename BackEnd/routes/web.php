<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminWebController;

// ========== ROOT REDIRECT ==========
Route::get('/', function () {
    return redirect('/admin/login');
});

// ========== ADMIN BLADE ROUTES ==========
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminWebController::class, 'login'])->name('login');
    Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [AdminWebController::class, 'manajemenLaporan'])->name('laporan');
    Route::get('/pemantauan', [AdminWebController::class, 'pemantauan'])->name('pemantauan');
    Route::get('/kategori', [AdminWebController::class, 'manajemenKategori'])->name('kategori');
    Route::get('/rekap', [AdminWebController::class, 'rekapLaporan'])->name('rekap');
    Route::get('/pengaturan', [AdminWebController::class, 'pengaturan'])->name('pengaturan');
});

// ========== PETUGAS BLADE ROUTES ==========
Route::prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/login', [AdminWebController::class, 'login'])->name('login');
    Route::get('/dashboard', [AdminWebController::class, 'petugasDashboard'])->name('dashboard');
    Route::get('/laporan', [AdminWebController::class, 'petugasDetailLaporan'])->name('laporan');
    Route::get('/riwayat', [AdminWebController::class, 'petugasRiwayat'])->name('riwayat');
    Route::get('/pengaturan', [AdminWebController::class, 'petugasPengaturan'])->name('pengaturan');
});

// ========== KOORDINATOR BLADE ROUTES ==========
Route::prefix('koordinator')->name('koordinator.')->group(function () {
    Route::get('/login', [AdminWebController::class, 'login'])->name('login');
    Route::get('/dashboard', [AdminWebController::class, 'koordinatorDashboard'])->name('dashboard');
    Route::get('/rekap', [AdminWebController::class, 'koordinatorRekap'])->name('rekap');
});
