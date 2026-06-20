<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KategoriSampah;
use App\Models\LaporanSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // --- LAPORAN CRUD ---
    public function getLaporan()
    {
        $laporan = LaporanSampah::with(['kategori', 'petugas'])->orderBy('created_at', 'desc')->get()->map(function ($item) {
            $item->url_foto_sebelum = asset('storage/sampah/' . $item->foto_sebelum);
            $item->url_foto_sesudah = $item->foto_sesudah ? asset('storage/sampah/' . $item->foto_sesudah) : null;
            return $item;
        });

        return response()->json(['status' => 'success', 'data' => $laporan], 200);
    }

    public function showLaporan($id)
    {
        $laporan = LaporanSampah::with(['kategori', 'petugas'])->find($id);
        if (!$laporan) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }
        $laporan->url_foto_sebelum = asset('storage/sampah/' . $laporan->foto_sebelum);
        $laporan->url_foto_sesudah = $laporan->foto_sesudah ? asset('storage/sampah/' . $laporan->foto_sesudah) : null;
        return response()->json(['status' => 'success', 'data' => $laporan], 200);
    }

    public function updateLaporan(Request $request, $id)
    {
        $laporan = LaporanSampah::find($id);
        if (!$laporan) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }

        $request->validate([
            'kategori_id' => 'sometimes|exists:kategori_sampah,id',
            'nama_pelapor' => 'sometimes|string',
            'latitude' => 'sometimes|string',
            'longitude' => 'sometimes|string',
            'deskripsi_singkat' => 'sometimes|string',
            'status' => 'sometimes|in:menunggu,diproses,selesai',
            'petugas_id' => 'nullable|exists:users,id',
        ]);

        $laporan->update($request->all());

        return response()->json(['status' => 'success', 'message' => 'Laporan berhasil diperbarui', 'data' => $laporan], 200);
    }

    public function deleteLaporan($id)
    {
        $laporan = LaporanSampah::find($id);
        if (!$laporan) {
            return response()->json(['status' => 'error', 'message' => 'Laporan tidak ditemukan'], 404);
        }
        $laporan->delete();
        return response()->json(['status' => 'success', 'message' => 'Laporan berhasil dihapus'], 200);
    }


    // --- KATEGORI CRUD ---
    public function getKategori()
    {
        $kategori = KategoriSampah::all();
        return response()->json(['status' => 'success', 'data' => $kategori], 200);
    }

    public function showKategori($id)
    {
        $kategori = KategoriSampah::find($id);
        if (!$kategori) {
            return response()->json(['status' => 'error', 'message' => 'Kategori tidak ditemukan'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $kategori], 200);
    }

    public function createKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_sampah,nama_kategori',
            'deskripsi_penanganan' => 'required|string',
        ]);

        $kategori = KategoriSampah::create($request->all());

        return response()->json(['status' => 'success', 'message' => 'Kategori berhasil ditambahkan', 'data' => $kategori], 201);
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = KategoriSampah::find($id);
        if (!$kategori) {
            return response()->json(['status' => 'error', 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_sampah,nama_kategori,' . $id,
            'deskripsi_penanganan' => 'required|string',
        ]);

        $kategori->update($request->all());

        return response()->json(['status' => 'success', 'message' => 'Kategori berhasil diperbarui', 'data' => $kategori], 200);
    }

    public function deleteKategori($id)
    {
        $kategori = KategoriSampah::find($id);
        if (!$kategori) {
            return response()->json(['status' => 'error', 'message' => 'Kategori tidak ditemukan'], 404);
        }
        $kategori->delete();
        return response()->json(['status' => 'success', 'message' => 'Kategori berhasil dihapus'], 200);
    }


    // --- USER/PETUGAS CRUD ---
    public function getUsers()
    {
        $users = User::all();
        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function showUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas',
            'wilayah_tugas' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'wilayah_tugas' => $request->wilayah_tugas,
        ]);

        return response()->json(['status' => 'success', 'message' => 'User berhasil dibuat', 'data' => $user], 201);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,petugas',
            'wilayah_tugas' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'wilayah_tugas' => $request->wilayah_tugas,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['status' => 'success', 'message' => 'User berhasil diperbarui', 'data' => $user], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus'], 200);
    }
}
