<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)
                    ->whereIn('role', ['admin', 'petugas', 'koordinator'])
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi salah'
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'       => true,
            'message'       => 'Login berhasil',
            'data'          => [
                'user'      => [
                    'id'    => $user->id,
                    'nama'  => $user->name,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                    'telepon' => $user->telepon ?? '',
                    'no_hp' => $user->telepon ?? '',
                    'lokasi_sekitar' => $user->lokasi_sekitar ?? '',
                    'created_at' => $user->created_at ? $user->created_at->format('Y-m-d') : '',
                ],
                'token'     => $token,
                'token_type'=> 'Bearer'
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data'    => [
                'id'    => $user->id,
                'nama'  => $user->name,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'no_hp' => $user->telepon ?? '-',
                'telepon' => $user->telepon ?? '-',
                'lokasi_sekitar' => $user->lokasi_sekitar ?? '-',
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $user->name = $request->nama ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->telepon = $request->no_hp ?? $user->telepon;
        $user->lokasi_sekitar = $request->lokasi_sekitar ?? $user->lokasi_sekitar;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate',
            'data' => ['nama' => $user->name, 'email' => $user->email, 'no_hp' => $user->telepon, 'lokasi_sekitar' => $user->lokasi_sekitar]
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini salah'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah']);
    }
}