<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function manajemenLaporan()
    {
        return view('admin.manajemen-laporan');
    }

    public function pemantauan()
    {
        return view('admin.pemantauan-penugasan');
    }

    public function manajemenKategori()
    {
        return view('admin.manajemen-kategori');
    }

    public function rekapLaporan()
    {
        return view('admin.rekap-laporan');
    }

    public function pengaturan()
    {
        return view('admin.pengaturan');
    }

    // Petugas
    public function petugasDashboard()
    {
        return view('petugas.dashboard');
    }

    public function petugasDetailLaporan()
    {
        return view('petugas.detail-laporan');
    }

    public function petugasRiwayat()
    {
        return view('petugas.riwayat');
    }

    public function petugasPengaturan()
    {
        return view('petugas.pengaturan');
    }

    // Koordinator
    public function koordinatorDashboard()
    {
        return view('admin.dashboard');
    }

    public function koordinatorRekap()
    {
        return view('admin.rekap-laporan');
    }
}
