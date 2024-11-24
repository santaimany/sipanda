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
    // Membuat pengajuan baru
    public function create(Request $request)
    {
        $validated = $request->validate([
            'desa_tujuan_id' => 'required|exists:desa,id',
            'jenis_pangan' => 'required|string',
            'berat' => 'required|numeric|min:1',
        ]);

        $user = $request->user();

        // Validasi apakah user memiliki desa terkait
        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak memiliki desa yang terkait.'], 400);
        }

        $desaAsal = Desa::find($user->desa_id);

        // Pastikan desa asal dan tujuan berbeda
        if ($desaAsal->id == $validated['desa_tujuan_id']) {
            return response()->json(['message' => 'Pengajuan tidak dapat dibuat ke desa sendiri.'], 400);
        }

        $desaTujuan = Desa::findOrFail($validated['desa_tujuan_id']);

        // Pastikan desa memiliki koordinat
        if (!$desaAsal->latitude || !$desaAsal->longitude || !$desaTujuan->latitude || !$desaTujuan->longitude) {
            return response()->json(['message' => 'Koordinat desa asal atau tujuan tidak ditemukan.'], 400);
        }

        // Cek apakah ada pengajuan pending untuk desa tujuan yang sama
        $existingPengajuan = Pengajuan::where('desa_asal_id', $desaAsal->id)
            ->where('desa_tujuan_id', $validated['desa_tujuan_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingPengajuan) {
            return response()->json(['message' => 'Pengajuan sudah ada dan masih pending. Tunggu hingga pengajuan diproses sebelum membuat pengajuan baru.'], 400);
        }

        // Validasi apakah jenis pangan ada di tabel jenis_pangan
        $jenisPangan = JenisPangan::where('nama_pangan', $validated['jenis_pangan'])->first();
        if (!$jenisPangan) {
            return response()->json(['message' => 'Jenis pangan tidak valid.'], 400);
        }

        // Hitung jarak menggunakan HERE Maps API
        $jarak = $this->calculateDistance(
            $desaAsal->latitude,
            $desaAsal->longitude,
            $desaTujuan->latitude,
            $desaTujuan->longitude
        );

        if (is_null($jarak)) {
            return response()->json(['message' => 'Gagal menghitung jarak menggunakan HERE Maps API.'], 500);
        }

        // Cek stok pangan di desa asal
        $pangan = Pangan::where('desa_id', $desaAsal->id)
            ->where('jenis_pangan', $validated['jenis_pangan'])
            ->first();

        if (!$pangan || $pangan->berat < $validated['berat']) {
            return response()->json(['message' => 'Stok pangan tidak mencukupi.'], 400);
        }

        // Hitung total harga dan ongkir
        $totalHarga = $validated['berat'] * $jenisPangan->harga;
        $ongkir = $jarak * $validated['berat'] * 500; // Ongkir per kilometer per kilogram
        $pajak = $totalHarga * 0.01; // Pajak 1% dari total harga

        // Buat nomor invoice unik
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        // Buat pengajuan baru
        $pengajuan = Pengajuan::create([
            'desa_asal_id' => $desaAsal->id,
            'desa_tujuan_id' => $validated['desa_tujuan_id'],
            'jenis_pangan' => $validated['jenis_pangan'],
            'berat' => $validated['berat'],
            'jarak' => $jarak,
            'total_harga' => $totalHarga,
            'ongkir' => $ongkir,
            'pajak' => $pajak,
            'status' => 'pending',
            'invoice_number' => $invoiceNumber,
        ]);

        return response()->json(['message' => 'Pengajuan berhasil dibuat.', 'data' => $pengajuan], 201);
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
                'harga_pangan_per_kg' => 'Rp ' . number_format($pengajuan->total_harga / $pengajuan->berat, 0, ',', '.'),
                'ongkos_pengiriman' => 'Rp ' . number_format($pengajuan->ongkir, 0, ',', '.'),
                'pajak' => 'Rp ' . number_format($pengajuan->pajak, 0, ',', '.'),
                'total' => 'Rp ' . number_format($pengajuan->total_harga + $pengajuan->ongkir + $pengajuan->pajak, 0, ',', '.'),
                'invoice_number' => $pengajuan->invoice_number,
            ];

            return response()->json(['message' => 'Invoice berhasil dibuat.', 'invoice' => $invoice], 200);
        }

        return response()->json(['message' => 'Pengajuan tidak berada dalam status pending.'], 400);
    }

    // Fungsi untuk menghitung jarak menggunakan HERE Maps API
    private function calculateDistance($latitudeAsal, $longitudeAsal, $latitudeTujuan, $longitudeTujuan)
    {
        $apiKey = env('HERE_API_KEY');

        if (!$apiKey) {
            return null;
        }

        $url = "https://router.hereapi.com/v8/routes";

        $params = [
            'transportMode' => 'car',
            'origin' => "{$latitudeAsal},{$longitudeAsal}",
            'destination' => "{$latitudeTujuan},{$longitudeTujuan}",
            'return' => 'summary',
            'apiKey' => $apiKey,
        ];

        $response = Http::get($url, $params);

        if ($response->failed() || empty($response->json()['routes'])) {
            return null;
        }

        $distanceMeters = $response->json()['routes'][0]['sections'][0]['summary']['length'] ?? 0;

        return $distanceMeters / 1000; // Konversi ke kilometer
    }
}
                                                                                        