<?php

namespace App\Http\Controllers\UserBapanas;

use App\Models\Desa;
use App\Models\Pangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JenisPangan;
use GuzzleHttp\Psr7\Message;

class PendataanController extends Controller
{
    /**
     * Tampilkan data desa untuk halaman pendataan.
     */
    public function showDesaData()
    {
        $dataDesa = Desa::with(['pangan' => function($query) {
            $query->orderBy('updated_at', 'desc'); // Urutkan pangan berdasarkan waktu update terakhir
        }])
        ->select('id', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan')
        ->get();

        // Modifikasi data desa untuk menambahkan nama lengkap dan informasi pangan serta last update
        $dataDesa = $dataDesa->map(function ($desa) {
        $desa->nama_lengkap = "{$desa->kelurahan}, {$desa->kecamatan}, {$desa->kabupaten}, {$desa->provinsi}";
        $desa->jumlah_pangan = $desa->pangan->count(); // Hitung jumlah pangan yang dimiliki desa
        
        // Last update untuk data pangan (ambil yang paling terbaru)
        $desa->last_update = $desa->pangan->isNotEmpty()
        ? $desa->pangan->first()['updated_at']->format('d-m-Y H:i:s')
        : 'Belum Diperbarui';

        // Gabungkan data pangan dan tanggal terakhir update untuk setiap pangan
        $desa->pangan = $desa->pangan->map(function ($pangan) {
            return [
                'nama_pangan' => $pangan->nama_pangan,
                'last_update' => $pangan->updated_at->format('d-m-Y H:i:s')  // Format tanggal update pangan
            ];
        });

        return $desa;
        });

        // Kembalikan data desa beserta pangan dan last update dalam format JSON
        return response()->json([
        'success' => true,
        'data' => $dataDesa,
        ]);

    }

    public function getDataPangan()
{
    $dataPangan = JenisPangan::all(['id', 'nama_pangan', 'harga']);
    return response()->json($dataPangan);
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

    // Ambil semua data dari tabel jenis_pangan
    $jenisPangan = JenisPangan::pluck('id')->toArray(); // Mengambil ID jenis_pangan untuk validasi

    // Validasi input dari form
    $validated = $request->validate([
        'jenis_pangan_id' => 'required|integer|in:' . implode(',', $jenisPangan), // Validasi ID jenis_pangan
        'berat' => 'required|numeric|min:0',
    ]);

    // Cari data jenis pangan berdasarkan ID untuk mendapatkan nama dan harga
    $selectedJenisPangan = JenisPangan::find($validated['jenis_pangan_id']);
    if (!$selectedJenisPangan) {
        return response()->json(['error' => 'Jenis pangan yang dipilih tidak valid.'], 400);
    }

    // Simpan data pangan ke desa yang dipilih
    Pangan::create([
        'desa_id' => $desa->id, // Gunakan desa_id yang telah divalidasi
        'jenis_pangan' => $selectedJenisPangan->nama_pangan, // Ambil nama dari jenis_pangan
        'berat' => $validated['berat'],
        'harga' => $selectedJenisPangan->harga, // Ambil harga dari jenis_pangan
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
        'berat' => 'nullable|numeric|min:0',          // nullable berarti tidak wajib
    ]);

    // Update hanya kolom yang ada di input, jika input tidak ada maka biarkan kolom tetap
    if ($request->filled('berat')) {
        $pangan->berat = $validated['berat'];
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
