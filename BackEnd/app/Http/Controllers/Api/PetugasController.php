<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function index()
    {
        return response()->json(Petugas::where('status_aktif', true)->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|unique:petugas',
            'email' => 'required|email|unique:petugas',
            'password' => 'required|string|min:6',
            'no_telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
            'area_tugas' => 'nullable|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $petugas = Petugas::create($validated);

        return response()->json($petugas, 201);
    }

    public function show($id)
    {
        return response()->json(Petugas::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::findOrFail($id);
        $petugas->update($request->except('password'));
        return response()->json($petugas);
    }

    public function destroy($id)
    {
        Petugas::findOrFail($id)->update(['status_aktif' => false]);
        return response()->json(['message' => 'Petugas dinonaktifkan']);
    }
}