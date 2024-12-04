<?php

namespace App\Http\Controllers\UserBapanas;

use App\Models\User;
use App\Models\JenisPangan;
use App\Models\HargaHistori;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DetailPanganController extends Controller
{
    public function index()
    {
        $data = JenisPangan::with('hargaHistori')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $data,
        ]);
    }

    // Update harga pangan
    public function updateHarga(Request $request, $id)
    {
        $validated = $request->validate([
            'harga' => 'required|numeric',
        ]);

        $jenisPangan = JenisPangan::findOrFail($id);

        // Simpan histori harga
        HargaHistori::create([
            'jenis_pangan_id' => $jenisPangan->id,
            'harga' => $jenisPangan->harga,
        ]);

        // Update harga saat ini
        $jenisPangan->update(['harga' => $validated['harga']]);

        $kepalaDesaUsers = User::where('role', 'kepala_desa')->get();

    foreach ($kepalaDesaUsers as $kades) {
    Notification::create([
        'user_id' => $kades->id,
        'desa_id' => $kades->desa_id,
        'title' => 'Update Harga Pangan',
        'message' => "Harga pangan untuk jenis {$jenisPangan->nama_pangan} telah diperbarui menjadi Rp{$jenisPangan->harga} oleh Bapanas.",
        'type' => 'harga_update',
        'is_read' => false,
    ]);
}

        return response()->json([
            'success' => true,
            'message' => 'Harga berhasil diupdate',
            'data' => $jenisPangan,
        ]);
    }

    public function insertData(Request $request)
    {
        $validated = $request->validate([
            'nama_pangan' => 'required|string|max:255',
            'harga' => 'required|numeric',
        ]);

        $newData = JenisPangan::create([
            'nama_pangan' => $validated['nama_pangan'],
            'harga' => $validated['harga'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data baru berhasil ditambahkan',
            'data' => $newData,
        ]);
        
    }

    // Melihat histori harga
    public function historiHarga($id)
    {
        $histori = HargaHistori::where('jenis_pangan_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Histori harga berhasil diambil',
            'data' => $histori,
        ]);
    }
}
