<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Desa;

class DesaSeeder extends Seeder
{
    public function run()
    {
        // Ambil data dari API
        $provinces = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json')->json();

        foreach ($provinces as $province) {
            $regencies = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$province['id']}.json")->json();
            foreach ($regencies as $regency) {
                $districts = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/districts/{$regency['id']}.json")->json();
                foreach ($districts as $district) {
                    $villages = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/villages/{$district['id']}.json")->json();
                    foreach ($villages as $village) {
                        // Simpan ke database
                        Desa::create([
                            'nama' => $village['name'],
                            'provinsi' => $province['name'],
                            'kabupaten' => $regency['name'],
                            'kecamatan' => $district['name'],
                            'kelurahan' => $village['name'],
                        ]);
                    }
                }
            }
        }
    }
}
