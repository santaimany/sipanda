<?php

namespace App\Http\Controllers\Bapanas;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Pangan;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    // Menyetujui pengajuan
    public function approve(Request $request, $id)
    {
        $user = $request->user();

        // Pastikan role adalah bapanas
        if ($user->role !== 'bapanas') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pengajuan = Pengajuan::findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini tidak dapat disetujui.'], 400);
        }

        // Kurangi stok pangan di desa asal
        $pangan = Pangan::where('desa_id', $pengajuan->desa_tujuan_id)
                        ->where('jenis_pangan', $pengajuan->jenis_pangan)
                        ->first();

        if (!$pangan || $pangan->berat < $pengajuan->berat) {
            return response()->json(['message' => 'Stok pangan tidak mencukupi.'], 400);
        }

        $pangan->berat -= $pengajuan->berat;
        $pangan->save();

        // Update status pengajuan dan set ID bapanas
        $pengajuan->update([
            'status' => 'approved',
            'bapanas_id' => $user->id, // Simpan ID bapanas yang menyetujui
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

        if ($pengajuan->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan ini tidak dapat ditolak.'], 400);
        }

        $validated = $request->validate([
            'alasan' => 'required|string',
        ]);

        // Update status pengajuan
        $pengajuan->update([
            'status' => 'rejected',
            'alasan' => $validated['alasan'],
            'bapanas_id' => $user->id, // Simpan ID bapanas yang menolak
        ]);

        return response()->json(['message' => 'Pengajuan berhasil ditolak.', 'data' => $pengajuan], 200);
    }

    // Mendapatkan semua pengajuan pending untuk bapanas
    public function getPendingPengajuan()
    {
        $pengajuan = Pengajuan::where('status', 'pending')->with(['desaAsal', 'desaTujuan'])->get();

        return response()->json(['data' => $pengajuan], 200);
    }

    // Mendapatkan riwayat persetujuan untuk bapanas
    public function getApprovedPengajuan(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'bapanas') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pengajuan = Pengajuan::where('bapanas_id', $user->id)
                              ->where('status', 'approved')
                              ->with(['desaAsal', 'desaTujuan'])
                              ->get();

        return response()->json(['data' => $pengajuan], 200);
    }
}
