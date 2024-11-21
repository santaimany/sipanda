<?php
namespace App\Http\Controllers\User;

use App\Models\Pangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PanganController extends Controller
{
    public function getPanganByUserDesa(Request $request)
    {
        $user = $request->user(); // Ambil user yang sedang login

        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak terkait dengan desa mana pun'], 403);
        }

        $pangans = Pangan::where('desa_id', $user->desa_id)->get();

        return response()->json([
            'message' => 'Data pangan ditemukan',
            'data' => $pangans,
        ]);
    }

    public function getPersentaseBeratByDesa(Request $request)
    {
        // Pastikan user terautentikasi
        $user = $request->user();

        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak terkait dengan desa mana pun'], 403);
        }

        // Ambil data pangan berdasarkan desa user
        $pangans = Pangan::where('desa_id', $user->desa_id)->get();

        if ($pangans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data pangan untuk desa ini'], 404);
        }

        // Hitung total berat
        $totalBerat = $pangans->sum('berat');

        // Hitung persentase berat untuk setiap jenis pangan
        $persentaseBerat = $pangans->groupBy('jenis_pangan')->map(function ($group) use ($totalBerat) {
            $totalJenisBerat = $group->sum('berat');
            return round(($totalJenisBerat / $totalBerat) * 100, 2);
        });

        return response()->json([
            'message' => 'Persentase berat berdasarkan jenis pangan',
            'data' => $persentaseBerat,
        ]);
    }
}
