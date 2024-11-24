<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JarakController extends Controller
{
    /**
     * Hitung jarak menggunakan HERE Maps API
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

        // HERE Maps API Key
        $apiKey = env('HERE_API_KEY');

        // URL Endpoint HERE Routing API
        $url = "https://router.hereapi.com/v8/routes?transportMode=car&origin=52.5308,13.3847&destination=52.5264,13.3686&return=summary&apikey=H0EiTnWe1ND3Yv6PqzSsr0Z0ppyetMuTmNDDF3Ac0-g";

        // Parameter untuk API  
        $params = [
            'transportMode' => 'car', // Mode transportasi
            'origin' => "{$request->latitude_asal},{$request->longitude_asal}", // Titik asal
            'destination' => "{$request->latitude_tujuan},{$request->longitude_tujuan}", // Titik tujuan
            'return' => 'summary', // Data ringkasan
            'apiKey' => $apiKey,
        ];

        // Kirim permintaan ke HERE API
        $response = Http::get($url, $params);

        // Periksa jika API gagal
        if ($response->failed()) {
            return response()->json([
                'message' => 'Gagal menghitung jarak. Periksa kembali input Anda atau API Key.',
            ], 500);
        }

        // Ambil jarak dalam meter dari respons
        $distanceMeters = $response->json()['routes'][0]['sections'][0]['summary']['length'];
        $distanceKilometers = $distanceMeters / 1000; // Konversi ke kilometer

        return response()->json([
            'message' => 'Perhitungan jarak berhasil.',
            'data' => [
                'distance_meters' => $distanceMeters,
                'distance_kilometers' => $distanceKilometers,
            ],
        ], 200);
    }
}
