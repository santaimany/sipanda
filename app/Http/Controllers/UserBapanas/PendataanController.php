<?php

namespace App\Http\Controllers\UserBapanas;

use App\Models\Desa;
use App\Models\Pangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Message;

class PendataanController extends Controller
{
    /**
     * Tampilkan data desa untuk halaman pendataan.
     */
    public function showDesaData()
    {
        $dataDesa = Desa::select('id', 'nama', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan')->get();

    // Tambahkan nama lengkap di sisi PHP
    $dataDesa = $dataDesa->map(function ($desa) {
        $desa->nama_lengkap = "{$desa->nama}, {$desa->kelurahan}, {$desa->kecamatan}, {$desa->kabupaten}, {$desa->provinsi}";
        return $desa;
    });

    // Kembalikan data desa dengan nama lengkap sebagai JSON
    return response()->json([
        'success' => true,
        'data' => $dataDesa,
    ]);

    }

    /**
     * Tambahkan data pangan berdasarkan input dari form.
     */
    public function formPanganData(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id',
            'jenis_pangan' => 'required|string|max:100',
            'berat' => 'required|numeric|min:0',
            'harga' => 'required|numeric|min:0',
        ]);

        // Simpan data pangan ke database
        Pangan::create([
            'desa_id' => $validated['desa_id'],
            'jenis_pangan' => $validated['jenis_pangan'],
            'berat' => $validated['berat'],
            'harga' => $validated['harga'],
            'tanggal' => now(),
        ]);

        return response()->json([
            'message' => 'Berhasil Memasukkan Data',
        ]);
    }
}
