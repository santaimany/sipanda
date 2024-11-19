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
    /**
     * Step 1: Register User (General Information)
     */
    public function registerIdentity(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:admin,kepala_desa,bapanas',
            'phone_number' => 'nullable|string|max:15',
        ]);

        // Hash password sebelum menyimpan
        $validated['password'] = Hash::make($validated['password']);

        // Simpan user ke database
        $user = User::create($validated);

        return response()->json([
            'message' => 'Step 1 completed. User created successfully.',
            'data' => $user,
        ], 201);
    }

    /**
     * Step 2: Assign Desa (Only for Kepala Desa)
     */
    public function registerKepalaDesa(Request $request, $userId)
    {
        // Validasi input
        $validated = $request->validate([
            'province_id' => 'required|integer',
            'regency_id' => 'required|integer',
            'district_id' => 'required|integer',
            'village_id' => 'required|integer',
        ]);

        // Fetch data desa dari API Emsifa
        $provinceResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/province/{$validated['province_id']}.json");
        $regencyResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regency/{$validated['regency_id']}.json");
        $districtResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/district/{$validated['district_id']}.json");
        $villageResponse = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/village/{$validated['village_id']}.json");

        if ($provinceResponse->failed() || $regencyResponse->failed() || $districtResponse->failed() || $villageResponse->failed()) {
            return response()->json(['message' => 'Data lokasi tidak ditemukan di API.'], 404);
        }

        $province = $provinceResponse->json();
        $regency = $regencyResponse->json();
        $district = $districtResponse->json();
        $village = $villageResponse->json();

        // Validasi apakah desa sudah memiliki kepala desa
        $existingKepalaDesa = Desa::where('provinsi', $province['name'])
            ->where('kabupaten', $regency['name'])
            ->where('kecamatan', $district['name'])
            ->where('kelurahan', $village['name'])
            ->whereNotNull('kepala_desa_id')
            ->exists();

        if ($existingKepalaDesa) {
            return response()->json([
                'message' => 'Desa ini sudah memiliki kepala desa.',
            ], 400);
        }

        // Cari atau buat desa di database
        $desa = Desa::firstOrCreate(
            [
                'provinsi' => $province['name'],
                'kabupaten' => $regency['name'],
                'kecamatan' => $district['name'],
                'kelurahan' => $village['name'],
            ],
            [
                'nama' => $village['name'],
            ]
        );

        // Update user dengan desa_id
        $user = User::findOrFail($userId);
        $user->desa_id = $desa->id;
        $user->save();

        // Tandai desa ini sudah memiliki kepala desa
        $desa->kepala_desa_id = $user->id;
        $desa->save();

        return response()->json([
            'message' => 'Step 2 completed. Desa assigned to user successfully.',
            'data' => [
                'user' => $user,
                'desa' => $desa,
            ],
        ], 200);
    }
}