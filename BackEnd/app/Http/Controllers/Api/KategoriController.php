<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;

class KategoriController extends Controller
{
    public function index()
    {
        return response()->json(KategoriSampah::where('status_aktif', true)->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'level_risiko' => 'required|in:rendah,sedang,tinggi',
        ]);

        $kategori = KategoriSampah::create($validated);
        return response()->json($kategori, 201);
    }

    public function show($id)
    {
        return response()->json(KategoriSampah::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriSampah::findOrFail($id);
        $kategori->update($request->all());
        return response()->json($kategori);
    }

    public function destroy($id)
    {
        KategoriSampah::findOrFail($id)->update(['status_aktif' => false]);
        return response()->json(['message' => 'Kategori dinonaktifkan']);
    }
}