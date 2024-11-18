<?php
namespace App\Http\Controllers\Location;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    private $baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api/';

    // Ambil daftar provinsi
    public function getProvinces()
    {
        $response = Http::get($this->baseUrl . 'provinces.json');
        return response()->json($response->json());
    }

    // Ambil daftar kabupaten berdasarkan province_id
    public function getRegencies($province_id)
    {
        $response = Http::get($this->baseUrl . "regencies/{$province_id}.json");
        return response()->json($response->json());
    }

    // Ambil daftar kecamatan berdasarkan regency_id
    public function getDistricts($regency_id)
    {
        $response = Http::get($this->baseUrl . "districts/{$regency_id}.json");
        return response()->json($response->json());
    }

    // Ambil daftar desa berdasarkan district_id
    public function getVillages($district_id)
    {
        $response = Http::get($this->baseUrl . "villages/{$district_id}.json");
        return response()->json($response->json());
    }
}