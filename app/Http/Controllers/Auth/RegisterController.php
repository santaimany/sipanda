<?php

namespace App\Http\Controllers\Auth;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
   

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:admin,kepala_desa,bapanas',
            'phone_number' => 'nullable|string|max:15',
            'province_id' => 'required|integer',
            'regency_id' => 'required|integer',
            'district_id' => 'required|integer',
            'village_id' => 'required|integer',
        ]);
    
        // Cari desa berdasarkan lokasi
        $desa = Desa::where('provinsi', $request->province_id)
                    ->where('kabupaten', $request->regency_id)
                    ->where('kecamatan', $request->district_id)
                    ->where('kelurahan', $request->village_id)
                    ->first();
    
        // Jika desa tidak ditemukan, kirim error
        if (!$desa) {
            return response()->json([
                'message' => 'Desa tidak ditemukan. Pastikan data lokasi benar.',
            ], 404);
        }
    
        // Periksa apakah sudah ada kepala desa untuk desa ini
        if ($request->role === 'kepala_desa') {
            $existingKepalaDesa = User::where('role', 'kepala_desa')
                ->where('desa_id', $desa->id)
                ->exists();
    
            if ($existingKepalaDesa) {
                return response()->json([
                    'message' => 'Kepala desa sudah terdaftar untuk desa ini.',
                ], 400);
            }
        }
    
        // Hash password
        $validated['password'] = Hash::make($validated['password']);
    
        // Tambahkan desa_id ke data user
        $validated['desa_id'] = $desa->id;
    
        // Simpan user
        $user = User::create($validated);
    
        return response()->json([
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }

    
}
