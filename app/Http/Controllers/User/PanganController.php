<?php
namespace App\Http\Controllers\User;

use App\Models\Desa;
use App\Models\Pangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

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

    public function getPanganDesaLain(Request $request)
    {
        // Fungsi yang Anda buat sebelumnya
        $user = $request->user();

        // Validasi apakah user memiliki desa yang terkait
        if (!$user->desa_id) {
            return response()->json(['message' => 'User tidak memiliki desa yang terkait.'], 400);
        }

        // Ambil desa user
        $desaUser = Desa::find($user->desa_id);

        if (!$desaUser || !$desaUser->latitude || !$desaUser->longitude) {
            return response()->json(['message' => 'Koordinat desa user tidak ditemukan.'], 400);
        }

        // Ambil data pangan dari desa lain
        $panganDesaLain = Pangan::where('desa_id', '!=', $desaUser->id)
            ->with('desa') // Untuk mendapatkan informasi desa terkait
            ->orderBy('jenis_pangan', 'asc')
            ->get();

        // Tambahkan perhitungan jarak untuk setiap desa
        $panganDesaLain = $panganDesaLain->map(function ($item) use ($desaUser) {
            $item->jarak = $this->calculateDistance(
                $desaUser->latitude,
                $desaUser->longitude,
                $item->desa->latitude,
                $item->desa->longitude
            );

            return $item;
        });

        return response()->json(['data' => $panganDesaLain], 200);
    }

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