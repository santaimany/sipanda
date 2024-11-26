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
    public function create(Request $request)
    {
        $validated = $request->validate([
            'desa_tujuan_id' => 'required|exists:desa,id',
            'jenis_pangan' => 'required|string',
            'berat' => 'required|numeric|min:1',
            'jasa_pengiriman' => 'required|string|in:JNE,SICEPAT,JNT', // Validasi jasa pengiriman
        ]);
    
        $user = $request->user();
    
        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak memiliki desa yang terkait.'], 400);
        }
    
        $desaAsal = Desa::find($user->desa_id);
    
        if ($desaAsal->id == $validated['desa_tujuan_id']) {
            return response()->json(['message' => 'Pengajuan tidak dapat dibuat ke desa sendiri.'], 400);
        }
    
        $desaTujuan = Desa::findOrFail($validated['desa_tujuan_id']);
    
        if (!$desaAsal->latitude || !$desaAsal->longitude || !$desaTujuan->latitude || !$desaTujuan->longitude) {
            return response()->json(['message' => 'Koordinat desa asal atau tujuan tidak ditemukan.'], 400);
        }
    
        $existingPengajuan = Pengajuan::where('desa_asal_id', $desaAsal->id)
            ->where('desa_tujuan_id', $validated['desa_tujuan_id'])
            ->where('status', 'pending')
            ->first();
    
        if ($existingPengajuan) {
            return response()->json(['message' => 'Pengajuan sudah ada dan masih pending. Tunggu hingga pengajuan diproses sebelum membuat pengajuan baru.'], 400);
        }
    
        $jenisPangan = JenisPangan::where('nama_pangan', $validated['jenis_pangan'])->first();
        if (!$jenisPangan) {
            return response()->json(['message' => 'Jenis pangan tidak valid.'], 400);
        }
    
        $jarak = $this->calculateDistance(
            $desaAsal->latitude,
            $desaAsal->longitude,
            $desaTujuan->latitude,
            $desaTujuan->longitude
        );
    
        if (is_null($jarak)) {
            return response()->json(['message' => 'Gagal menghitung jarak menggunakan Haversine formula.'], 500);
        }
    
        $pangan = Pangan::where('desa_id', $desaTujuan->id)
            ->where('jenis_pangan', $validated['jenis_pangan'])
            ->first();
    
        if (!$pangan || $pangan->berat < $validated['berat']) {
            return response()->json(['message' => 'Stok pangan tidak mencukupi.'], 400);
        }
    
        // Perhitungan ongkir berdasarkan jasa pengiriman
        $tarifPerKgPerKm = 0;
        switch ($validated['jasa_pengiriman']) {
            case 'JNE':
                $tarifPerKgPerKm = 6; // Tarif JNE: Rp6 per kg per km
                break;
            case 'SICEPAT':
                $tarifPerKgPerKm = 5; // Tarif SICEPAT: Rp5 per kg per km
                break;
            case 'JNT':
                $tarifPerKgPerKm = 4; // Tarif JNT: Rp4 per kg per km
                break;
            default:
                return response()->json(['message' => 'Jasa pengiriman tidak valid.'], 400);
        }
    
        $totalHarga = $validated['berat'] * $jenisPangan->harga;
        $ongkir = $jarak * $validated['berat'] * $tarifPerKgPerKm;
        $pajak = $totalHarga * 0.01;
    
        $invoiceNumber = 'INV-' . strtoupper(uniqid());
    
        $pengajuan = Pengajuan::create([
            'desa_asal_id' => $desaAsal->id,
            'desa_tujuan_id' => $validated['desa_tujuan_id'],
            'jenis_pangan' => $validated['jenis_pangan'],
            'berat' => $validated['berat'],
            'jarak' => $jarak,
            'total_harga' => $totalHarga,
            'ongkir' => $ongkir,
            'pajak' => $pajak,
            'jasa_pengiriman' => $validated['jasa_pengiriman'], // Simpan jasa pengiriman
            'status' => 'pending',
            'invoice_number' => $invoiceNumber,
        ]);
    
        return response()->json([
            'message' => 'Pengajuan berhasil dibuat.',
            'data' => [
                'invoice_number' => $pengajuan->invoice_number,
                'jasa_pengiriman' => $validated['jasa_pengiriman'],
                'total_harga' => $this->formatRupiah($totalHarga),
                'ongkir' => $this->formatRupiah($ongkir),
                'pajak' => $this->formatRupiah($pajak),
                'total' => $this->formatRupiah($totalHarga + $ongkir + $pajak),
            ],
        ], 201);
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
                                                                                        