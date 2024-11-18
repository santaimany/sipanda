<?php

namespace App\Http\Controllers\Auth;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
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

        // Fetch data from API
        $provinceResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/province/{$validated['province_id']}.json");
        $regencyResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regency/{$validated['regency_id']}.json");
        $districtResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/district/{$validated['district_id']}.json");
        $villageResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/village/{$validated['village_id']}.json");

        if ($provinceResponse->failed() || $regencyResponse->failed() || $districtResponse->failed() || $villageResponse->failed()) {
            return response()->json([
                'message' => 'Data lokasi tidak ditemukan di API.',
            ], 404);
        }

        $province = $provinceResponse->json();
        $regency = $regencyResponse->json();
        $district = $districtResponse->json();
        $village = $villageResponse->json();

        // Periksa apakah sudah ada kepala desa untuk desa ini
        if ($request->role === 'kepala_desa') {
            $existingKepalaDesa = User::where('role', 'kepala_desa')
                ->where('village_id', $validated['village_id'])
                ->exists();

            if ($existingKepalaDesa) {
                return response()->json([
                    'message' => 'Kepala desa sudah terdaftar untuk desa ini.',
                ], 400);
            }
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Cari desa berdasarkan lokasi
        $desa = Desa::where('provinsi', $province['name'])
                    ->where('kabupaten', $regency['name'])
                    ->where('kecamatan', $district['name'])
                    ->where('kelurahan', $village['name'])
                    ->first();

        if (!$desa) {
            // Jika desa belum ada, buat desa baru
            $desa = Desa::create([
                'nama' => $village['name'],
                'provinsi' => $province['name'],
                'kabupaten' => $regency['name'],
                'kecamatan' => $district['name'],
                'kelurahan' => $village['name'],
            ]);
        }

        // Simpan user
        $user = User::create(array_merge($validated, ['desa_id' => $desa->id]));

        // Jika user adalah kepala desa, update desa dengan ID kepala desa
        if ($user->role === 'kepala_desa') {
            $desa->update(['kepala_desa_id' => $user->id]);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }
}