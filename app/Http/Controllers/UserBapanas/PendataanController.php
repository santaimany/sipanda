<?php

namespace App\Http\Controllers\UserBapanas;

use App\Models\Desa;
use App\Models\Pangan;
use App\Models\JenisPangan;
use App\Models\Notification;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
     */public function insertPanganData(Request $request, $desa_id)
{
    $desa = Desa::where('id', $desa_id)->whereNotNull('kepala_desa_id')->first();
    if (!$desa) {
        return response()->json(['error' => 'Desa tidak valid atau tidak memiliki kepala desa.'], 404);
    }

    $jenisPangan = JenisPangan::pluck('id')->toArray();

    $validated = $request->validate([
        'jenis_pangan_id' => 'required|integer|in:' . implode(',', $jenisPangan),
        'berat' => 'required|numeric|min:0',
    ]);

    $selectedJenisPangan = JenisPangan::find($validated['jenis_pangan_id']);
    if (!$selectedJenisPangan) {
        return response()->json(['error' => 'Jenis pangan yang dipilih tidak valid.'], 400);
    }

    Pangan::create([
        'desa_id' => $desa->id,
        'jenis_pangan' => $selectedJenisPangan->nama_pangan,
        'berat' => $validated['berat'],
        'harga' => $selectedJenisPangan->harga,
        'tanggal' => now(),
    ]);

    // Ambil user_id Kepala Desa
    $userId = $desa->kepala_desa_id;

    if ($userId) {
        // Kirim notifikasi ke Kepala Desa
        Notification::create([
            'user_id' => $userId,
            'desa_id' => $desa->id,
            'title' => 'Data Pangan Ditambahkan',
            'message' => "Jenis pangan {$selectedJenisPangan->nama_pangan} telah ditambahkan ke desa {$desa->nama_lengkap} oleh Bapanas.",
            'type' => 'insert',
            'is_read' => false,
        ]);
    }

    return response()->json(['message' => 'Berhasil Memasukkan Data']);
}

public function updatePanganData(Request $request, $pangan_id)
{
    $pangan = Pangan::find($pangan_id);

    if (!$pangan) {
        return response()->json(['error' => 'Data pangan tidak ditemukan.'], 404);
    }

    $validated = $request->validate([
        'berat' => 'nullable|numeric|min:0',
    ]);

    if ($request->filled('berat')) {
        $pangan->berat = $validated['berat'];
    }

    $pangan->tanggal = now();
    $pangan->save();

    // Ambil desa terkait dan user_id Kepala Desa
    $desa = $pangan->desa;
    $userId = $desa->kepala_desa_id; // ID Kepala Desa
    $namaPangan = $pangan->jenis_pangan;

    if ($userId) {
        // Kirim notifikasi ke Kepala Desa terkait
        Notification::create([
            'user_id' => $userId, // Ditujukan ke Kepala Desa
            'desa_id' => $desa->id,
            'title' => 'Data Pangan Diperbarui',
            'message' => "Data pangan {$namaPangan} di desa {$desa->nama_lengkap} telah diperbarui oleh Bapanas.",
            'type' => 'update',
            'is_read' => false,
        ]);
    }

    return response()->json(['message' => 'Data pangan berhasil diperbarui.']);
}

public function deletePanganData($pangan_id)
{
    $pangan = Pangan::find($pangan_id);
    if (!$pangan) {
        return response()->json(['error' => 'Data pangan tidak ditemukan.'], 404);
    }

    $desa = $pangan->desa;
    $userId = $desa->kepala_desa_id; // ID Kepala Desa
    $namaPangan = $pangan->jenis_pangan;

    $pangan->delete();

    if ($userId) {
        // Kirim notifikasi ke Kepala Desa
        Notification::create([
            'user_id' => $userId,
            'desa_id' => $desa->id,
            'title' => 'Data Pangan Dihapus',
            'message' => "Data pangan {$namaPangan} di desa {$desa->nama_lengkap} telah dihapus oleh Bapanas.",
            'type' => 'delete',
            'is_read' => false,
        ]);
    }

    return response()->json(['message' => 'Data pangan berhasil dihapus']);
}

}
