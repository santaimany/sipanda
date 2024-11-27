<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JarakController extends Controller
{
    /**
     * Hitung jarak garis lurus (great-circle distance) antara dua titik geografis
     */
    public function calculateDistance(Request $request)
    {
        // Validasi input
        $request->validate([
            'latitude_asal' => 'required|numeric',
            'longitude_asal' => 'required|numeric',
            'latitude_tujuan' => 'required|numeric',
            'longitude_tujuan' => 'required|numeric',
        ]);

        // Ambil input
        $latitudeAsal = $request->latitude_asal;
        $longitudeAsal = $request->longitude_asal;
        $latitudeTujuan = $request->latitude_tujuan;
        $longitudeTujuan = $request->longitude_tujuan;

        // Hitung jarak garis lurus
        $distanceKilometers = $this->haversine(
            $latitudeAsal,
            $longitudeAsal,
            $latitudeTujuan,
            $longitudeTujuan
        );

        return response()->json([
            'message' => 'Perhitungan jarak garis lurus berhasil.',
            'data' => [
                'distance_kilometers' => $distanceKilometers,
            ],
        ], 200);
    }

    /**
     * Haversine Formula
     */
    private function haversine($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        // Konversi derajat ke radian
        $latFrom = deg2rad($latitude1);
        $lonFrom = deg2rad($longitude1);
        $latTo = deg2rad($latitude2);
        $lonTo = deg2rad($longitude2);

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
