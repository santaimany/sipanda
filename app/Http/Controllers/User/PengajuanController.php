<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Pangan;
use App\Models\JenisPangan;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PengajuanController extends Controller
{
    private function formatRupiah($number)
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}

    // Membuat pengajuan baru
    public function cekKetersediaan(Request $request)
    {
        $validated = $request->validate([
            'desa_tujuan_id' => 'required|exists:desa,id',
            'jenis_pangan' => 'required|string',
            'berat' => 'required|numeric|min:1',
        ]);
    
        $pangan = Pangan::where('desa_id', $validated['desa_tujuan_id'])
            ->where('jenis_pangan', $validated['jenis_pangan'])
            ->first();
    
        if (!$pangan || $pangan->berat < $validated['berat']) {
            return response()->json(['message' => 'Stok pangan tidak mencukupi.'], 400);
        }
    
        return response()->json(['message' => 'Stok pangan tersedia.'], 200);
    }
    // Simulasi perhitungan invoice tanpa menyimpan ke database
public function simulateInvoice(Request $request)
{
    $validated = $request->validate([
        'desa_tujuan_id' => 'required|exists:desa,id',
        'jenis_pangan' => 'required|string',
        'berat' => 'required|numeric|min:1',
        'jasa_pengiriman' => 'required|string|in:JNE,SICEPAT,JNT',
    ]);

    $user = $request->user();

    if (!$user->desa_id) {
        return response()->json(['message' => 'User tidak memiliki desa yang terkait.'], 400);
    }

    $desaAsal = Desa::find($user->desa_id);
    $desaTujuan = Desa::findOrFail($validated['desa_tujuan_id']);

    if ($desaAsal->id == $desaTujuan->id) {
        return response()->json(['message' => 'Tidak dapat mengajukan ke desa sendiri.'], 400);
    }

    $jarak = $this->calculateDistance(
        $desaAsal->latitude,
        $desaAsal->longitude,
        $desaTujuan->latitude,
        $desaTujuan->longitude
    );

    if (is_null($jarak)) {
        return response()->json(['message' => 'Gagal menghitung jarak.'], 500);
    }

    $jenisPangan = JenisPangan::where('nama_pangan', $validated['jenis_pangan'])->first();
    if (!$jenisPangan) {
        return response()->json(['message' => 'Jenis pangan tidak valid.'], 400);
    }

    // Hitung ongkir berdasarkan jasa pengiriman
    $tarifPerKgPerKm = match ($validated['jasa_pengiriman']) {
        'JNE' => 6,
        'SICEPAT' => 5,
        'JNT' => 4,
        default => 0,
    };

    $totalHarga = $validated['berat'] * $jenisPangan->harga;
    $ongkir = $jarak * $validated['berat'] * $tarifPerKgPerKm;
    $pajak = $totalHarga * 0.01;
    $total = $totalHarga + $ongkir + $pajak;

    return response()->json([
        'desa_penerima' => $desaTujuan->nama,
        'desa_pengirim' => $desaAsal->nama,
        'jarak' => round($jarak, 2) . ' KM',
        'jenis_pangan' => $jenisPangan->nama_pangan,
        'berat_diajukan' => $validated['berat'] . ' kg',
        'harga_per_kg' => $this->formatRupiah($jenisPangan->harga),
        'ongkir' => $this->formatRupiah($ongkir),
        'pajak' => $this->formatRupiah($pajak),
        'total' => $this->formatRupiah($total),
    ]);
}


    public function submitPengajuan(Request $request)
{
    $validated = $request->validate([
        'desa_tujuan_id' => 'required|exists:desa,id',
        'jenis_pangan' => 'required|string',
        'berat' => 'required|numeric|min:1',
        'jasa_pengiriman' => 'required|string|in:JNE,SICEPAT,JNT',
    ]);

    $user = $request->user();

    if (!$user->desa_id) {
        return response()->json(['message' => 'User tidak memiliki desa yang terkait.'], 400);
    }

    $desaAsal = Desa::find($user->desa_id);
    $desaTujuan = Desa::findOrFail($validated['desa_tujuan_id']);

    if ($desaAsal->id == $validated['desa_tujuan_id']) {
        return response()->json(['message' => 'Tidak dapat mengajukan ke desa sendiri.'], 400);
    }

    $jenisPangan = JenisPangan::where('nama_pangan', $validated['jenis_pangan'])->first();
    if (!$jenisPangan) {
        return response()->json(['message' => 'Jenis pangan tidak valid.'], 400);
    }

    $pangan = Pangan::where('desa_id', $validated['desa_tujuan_id'])
    ->where('jenis_pangan', $validated['jenis_pangan'])
    ->first();

    if (!$pangan || $pangan->berat < $validated['berat']) {
        return response()->json(['message' => 'Stok pangan tidak mencukupi.'], 400);
    }


    $jarak = $this->calculateDistance(
        $desaAsal->latitude,
        $desaAsal->longitude,
        $desaTujuan->latitude,
        $desaTujuan->longitude
    );

    if (is_null($jarak)) {
        return response()->json(['message' => 'Gagal menghitung jarak.'], 500);
    }

    $tarifPerKgPerKm = match ($validated['jasa_pengiriman']) {
        'JNE' => 6,
        'SICEPAT' => 5,
        'JNT' => 4,
        default => 0,
    };

    $totalHarga = $validated['berat'] * $jenisPangan->harga;
    $ongkir = $jarak * $validated['berat'] * $tarifPerKgPerKm;
    $pajak = $totalHarga * 0.01;

    $pengajuan = Pengajuan::create([
        'desa_asal_id' => $desaAsal->id,
        'desa_tujuan_id' => $desaTujuan->id,
        'jenis_pangan' => $validated['jenis_pangan'],
        'berat' => $validated['berat'],
        'jarak' => $jarak,
        'total_harga' => $totalHarga,
        'ongkir' => $ongkir,
        'pajak' => $pajak,
        'jasa_pengiriman' => $validated['jasa_pengiriman'],
        'status' => 'pending',
    ]);

    return response()->json(['message' => 'Pengajuan berhasil diajukan ke Bapanas.', 'data' => $pengajuan], 201);
}

    
    // Endpoint untuk mendapatkan invoice
    public function getInvoice($id)
    {
        $pengajuan = Pengajuan::with(['desaAsal', 'desaTujuan'])->findOrFail($id);
    
        if ($pengajuan->status === 'pending') {
            $invoice = [
                'desa_pengirim' => $pengajuan->desaAsal->nama,
                'desa_penerima' => $pengajuan->desaTujuan->nama,
                'jenis_pangan' => $pengajuan->jenis_pangan,
                'berat_diajukan' => $pengajuan->berat . ' kg',
                'harga_pangan_per_kg' => $this->formatRupiah($pengajuan->total_harga / $pengajuan->berat),
                'ongkos_pengiriman' => $this->formatRupiah($pengajuan->ongkir),
                'pajak' => $this->formatRupiah($pengajuan->pajak),
                'total' => $this->formatRupiah($pengajuan->total_harga + $pengajuan->ongkir + $pengajuan->pajak),
                'invoice_number' => $pengajuan->invoice_number,
            ];
    
            return response()->json(['message' => 'Invoice berhasil dibuat.', 'invoice' => $invoice], 200);
        }
    
        return response()->json(['message' => 'Pengajuan tidak berada dalam status pending.'], 400);
    }
    public function getRiwayatPengajuan(Request $request)
    {
        $user = $request->user();
    
        // Pastikan user memiliki desa terkait
        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak terkait dengan desa mana pun'], 403);
        }
    
        // Ambil semua pengajuan terkait desa user
        $pengajuan = Pengajuan::where('desa_asal_id', $user->desa_id)
            ->with('desaTujuan') // Ambil hanya informasi desa tujuan
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Format data untuk respons
        $riwayat = $pengajuan->map(function ($item) {
            return [
                'id' => $item->id,
                'desa_dituju' => $item->desaTujuan->nama,
                'status' => ucfirst($item->status),
                'jenis_pangan' => $item->jenis_pangan,
                'berat' => $item->berat . ' kg',
            ];
        });
    
        return response()->json([
            'message' => 'Riwayat pengajuan berhasil diambil.',
            'data' => $riwayat,
        ]);
    }
    public function getPengajuanDetail($id)
{
    $pengajuan = Pengajuan::with(['desaAsal', 'desaTujuan'])->findOrFail($id);

    return response()->json([
        'message' => 'Detail pengajuan berhasil diambil.',
        'data' => [
            'id' => $pengajuan->id,
            'desa_pengirim' => $pengajuan->desaAsal->nama,
            'desa_penerima' => $pengajuan->desaTujuan->nama,
            'jenis_pangan' => $pengajuan->jenis_pangan,
            'berat' => $pengajuan->berat . ' kg',
            'harga_per_kg' => $this->formatRupiah($pengajuan->total_harga / $pengajuan->berat),
            'total_harga' => $this->formatRupiah($pengajuan->total_harga),
            'ongkir' => $this->formatRupiah($pengajuan->ongkir),
            'pajak' => $this->formatRupiah($pengajuan->pajak),
            'jasa_pengiriman' => $pengajuan->jasa_pengiriman,
            'status' => ucfirst($pengajuan->status),
            'created_at' => $pengajuan->created_at->format('d M Y H:i:s'),
        ],
    ]);
}


    // Fungsi untuk menghitung jarak menggunakan HERE Maps API
    private function calculateDistance($latitudeAsal, $longitudeAsal, $latitudeTujuan, $longitudeTujuan)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        // Konversi derajat ke radian
        $latFrom = deg2rad($latitudeAsal);
        $lonFrom = deg2rad($longitudeAsal);
        $latTo = deg2rad($latitudeTujuan);
        $lonTo = deg2rad($longitudeTujuan);
    
        // Haversine formula
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
    
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        return $earthRadius * $c; // Jarak dalam kilometer
    }
}
                                                                                        