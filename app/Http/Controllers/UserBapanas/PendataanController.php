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
    public function insertPanganData(Request $request, $desa_id)
    {
        // Validasi bahwa desa_id valid dan desa tersebut memiliki kepala desa
        $desa = Desa::where('id', $desa_id)->whereNotNull('kepala_desa_id')->first();
        if (!$desa) {
            return response()->json(['error' => 'Desa tidak valid atau tidak memiliki kepala desa.'], 404);
        }

        // Validasi input dari form
        $validated = $request->validate([
            'jenis_pangan' => 'required|string|max:100',
            'berat' => 'required|numeric|min:0',
            'harga' => 'required|numeric|min:0',
        ]);

        // Simpan data pangan ke desa yang dipilih
        Pangan::create([
            'desa_id' => $desa->id, // Gunakan desa_id yang telah divalidasi
            'jenis_pangan' => $validated['jenis_pangan'],
            'berat' => $validated['berat'],
            'harga' => $validated['harga'],
            'tanggal' => now(),
        ]);

        return response()->json(['message' => 'Berhasil Memasukkan Data']);
    }

    public function updatePanganData(Request $request, $pangan_id)
{
    // Ambil data pangan berdasarkan ID
    $pangan = Pangan::find($pangan_id);

    // Jika data tidak ditemukan, kembalikan error
    if (!$pangan) {
        return response()->json(['error' => 'Data pangan tidak ditemukan.'], 404);
    }

    // Validasi input dari form, hanya validasi untuk yang dikirim
    $validated = $request->validate([
        'jenis_pangan' => 'nullable|string|max:100',  // nullable berarti tidak wajib
        'berat' => 'nullable|numeric|min:0',          // nullable berarti tidak wajib
        'harga' => 'nullable|numeric|min:0',          // nullable berarti tidak wajib
    ]);

    // Update hanya kolom yang ada di input, jika input tidak ada maka biarkan kolom tetap
    if ($request->has('jenis_pangan')) {
        $pangan->jenis_pangan = $validated['jenis_pangan'];
    }

    if ($request->has('berat')) {
        $pangan->berat = $validated['berat'];
    }

    if ($request->has('harga')) {
        $pangan->harga = $validated['harga'];
    }

    // Simpan data yang telah diperbarui
    $pangan->tanggal = now(); // atau bisa menggunakan tanggal yang dikirimkan jika ada input
    $pangan->save();

    return response()->json(['message' => 'Data pangan berhasil diperbarui.']);
}

    public function deletePanganData($pangan_id)
    {
        // Validasi bahwa data pangan dengan ID tertentu ada
        $pangan = Pangan::find($pangan_id);
        if (!$pangan) {
            return response()->json(['error' => 'Data pangan tidak ditemukan.'], 404);
        }

        // Hapus data pangan
        $pangan->delete();

        return response()->json(['message' => 'Data pangan berhasil dihapus']); 
    }
}
