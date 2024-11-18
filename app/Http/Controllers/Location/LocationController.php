<?php

namespace App\Http\Controllers\Location;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = '7bd395a20296186f823863c572df42f5'; // API Key Anda
    }

    public function getProvinces()
    {
        $response = $this->client->get('https://api.rajaongkir.com/starter/province', [
            'headers' => [
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return response()->json($data['rajaongkir']['results']);
    }

    public function getRegencies($province_id)
    {
        $response = $this->client->get("https://api.rajaongkir.com/starter/city?province=$province_id", [
            'headers' => [
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return response()->json($data['rajaongkir']['results']);
    }

    public function getDistricts($regency_id)
    {
        $response = $this->client->get("https://api.rajaongkir.com/starter/subdistrict?city=$regency_id", [
            'headers' => [
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return response()->json($data['rajaongkir']['results']);
    }

    public function getVillages($district_id)
    {
        $response = $this->client->get("https://api.rajaongkir.com/starter/village?subdistrict=$district_id", [
            'headers' => [
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return response()->json($data['rajaongkir']['results']);
    }
}