<?php

namespace App\Http\Controllers\UserBapanas;

use App\Models\Pangan;
use App\Models\Pengajuan;
use App\Models\JenisPangan;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{

    public function getUserBapanas()
    {
        $user = Auth::user();

        // Pastikan user memiliki role 'kepala_desa'
        if ($user->role !== 'bapanas') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ambil data nama user dan nama desa
        $data = [
            'name' => $user->name,

        ];

        return response()->json([
            'message' => 'Success',
            'data' => $data,
        ], 200);
    }

    // Menyetujui pengajuan
    public function approve(Request $request, $id)
    {
        $user = $request->user();

        // Pastikan role adalah bapanas
        if ($user->role !== 'bapanas') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pengajuan = Pengajuan::findOrFail($id);

        // Pastikan pengajuan berstatus pending
        if ($pengajuan->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini tidak dapat disetujui.'], 400);
        }

        // Kurangi stok pangan di desa asal
        $panganDesaTujuan = Pangan::where('desa_id', $pengajuan->desa_tujuan_id)
            ->where('jenis_pangan', $pengajuan->jenis_pangan)
            ->first();

        if ($panganDesaTujuan->berat < $pengajuan->berat) {
            return response()->json(['message' => 'Stok Pangan di desa asal tidak mencukupi.'], 400);
        }

        $panganDesaTujuan->berat -= $pengajuan->berat;
        $panganDesaTujuan->save();

        // Ambil harga dari tabel jenis_pangan
        $jenisPangan = JenisPangan::where('nama_pangan', $pengajuan->jenis_pangan)->first();
        if (!$jenisPangan) {
            return response()->json(['message' => 'Jenis pangan tidak ditemukan di tabel jenis_pangan.'], 400);
        }

        $hargaPangan = $jenisPangan->harga;

        // Tambahkan stok ke desa tujuan atau buat data pangan baru
        $panganDesaAsal = Pangan::where('desa_id', $pengajuan->desa_asal_id)
            ->where('jenis_pangan', $pengajuan->jenis_pangan)
            ->first();

            

        if ($panganDesaAsal) {
            // Jika desa tujuan sudah memiliki data pangan, tambahkan stok
            $panganDesaAsal->berat += $pengajuan->berat;
            $panganDesaAsal->save();
        } else {
            // Jika tidak ada, buat data pangan baru
            Pangan::create([
                'desa_id' => $pengajuan->desa_asal_id,
                'jenis_pangan' => $pengajuan->jenis_pangan,
                'berat' => $pengajuan->berat,
                'tanggal'=> now(),
                'harga' => $hargaPangan, // Harga diambil dari tabel jenis_pangan
            ]);
        }

        // Perbarui status pengajuan dan set ID bapanas
        $pengajuan->update([
            'status' => 'approved',
            'bapanas_id' => $user->id, // Simpan ID bapanas yang menyetujui
        ]);

        Notification::create([
            'user_id' => $pengajuan->desaAsal->kepala_desa_id, // User ID Kepala Desa
            'desa_id' => $pengajuan->desa_asal_id,
            'title' => 'Update Status Pengajuan',
            'message' => "Pengajuan Anda dengan ID #{$pengajuan->invoice_number} telah {$pengajuan->status}. oleh Staff Bapanas {$user->nama}",
            'type' => 'approval',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Pengajuan berhasil disetujui.', 'data' => $pengajuan], 200);
    }

    // Menolak pengajuan
    public function reject(Request $request, $id)
    {
        $user = $request->user();

        // Pastikan role adalah bapanas
        if ($user->role !== 'bapanas') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pengajuan = Pengajuan::findOrFail($id);

        // Pastikan pengajuan berstatus pending
        if ($pengajuan->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini tidak dapat ditolak.'], 400);
        }

        $validated = $request->validate([
            'alasan' => 'required|string',
        ]);

        // Update status pengajuan menjadi rejected
        $pengajuan->update([
            'status' => 'rejected',
            'alasan' => $validated['alasan'],
            'bapanas_id' => $user->id, // Simpan ID bapanas yang menolak
        ]);

        Notification::create([
            'user_id' => $pengajuan->desaAsal->kepala_desa_id, // User ID Kepala Desa
            'desa_id' => $pengajuan->desa_asal_id,
            'title' => 'Update Status Pengajuan',
            'message' => "Pengajuan Anda dengan ID #{$pengajuan->invoice_number} telah di {$pengajuan->status}. oleh Staff Bapanas {$user->nama}",
            'type' => 'rejected',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Pengajuan berhasil ditolak.', 'data' => $pengajuan], 200);
    }
    public function getPendingPengajuan()
    {
        $pengajuan = Pengajuan::where('status', 'pending')->with(['desaAsal', 'desaTujuan'])->get();

        return response()->json(['data' => $pengajuan], 200);
    }

    // Mendapatkan riwayat persetujuan untuk bapanas
    public function getApprovedPengajuan(Request $request)
    {
        $user = $request->user();

        // Pastikan role adalah bapanas
        if ($user->role !== 'bapanas') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pengajuan = Pengajuan::where('status', 'approved')
            ->with(['desaAsal', 'desaTujuan'])
            ->get();

        return response()->json(['data' => $pengajuan], 200);
    }

    public function getRejectedPengajuan(Request $request)
{
    $user = $request->user();

    // Pastikan role adalah bapanas
    if ($user->role !== 'bapanas') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Ambil pengajuan dengan status rejected
    $rejectedPengajuan = Pengajuan::where('status', 'rejected')
        ->with(['desaAsal', 'desaTujuan'])
        ->get();

    return response()->json(['data' => $rejectedPengajuan], 200);
}
}