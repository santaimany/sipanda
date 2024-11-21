<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardKepalaDesaController extends Controller
{
    public function getUserKades()
    {
        $user = Auth::user();

        // Pastikan user memiliki role 'kepala_desa'
        if ($user->role !== 'kepala_desa') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ambil data nama user dan nama desa
        $data = [
            'name' => $user->name,
            'desa' => $user->desa ? $user->desa->nama : 'Desa tidak ditemukan'
        ];

        return response()->json([
            'message' => 'Success',
            'data' => $data,
        ], 200);
    }

    public function getGreetingKades()
    {
        $user = Auth::user();

        if ($user && $user->desa) {
            $desa = $user->desa;
            return response()->json([
                'greeting' => "Selamat datang, {$user->name}!",
                'address' => [
                    'provinsi' => $desa->provinsi,
                    'kabupaten' => $desa->kabupaten,
                    'kecamatan' => $desa->kecamatan,
                    'kelurahan' => $desa->kelurahan,
                    'desa' => $desa->nama,
                ],
            ]);
        }

        return response()->json([
            'greeting' => "Selamat datang, {$user->name}!",
            'address' => 'Alamat tidak tersedia.',
        ]);
    }
}
