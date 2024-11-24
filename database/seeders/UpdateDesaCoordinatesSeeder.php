<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Desa;
use Illuminate\Support\Facades\Http;

class UpdateDesaCoordinatesSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua desa yang belum memiliki koordinat
        $desaList = Desa::whereNull('latitude')->orWhereNull('longitude')->get();

        foreach ($desaList as $desa) {
            // Buat query lokasi
            $query = "{$desa->nama}, Indonesia";


            echo "Mencari koordinat untuk: {$query}\n";

            // Request ke API Nominatim
            $response = Http::withHeaders([
                'User-Agent' => 'DesaLocator/1.0 (example@example.com)',
            ])->get("https://nominatim.openstreetmap.org/search", [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
            ]);
            
            if ($response->successful()) {
                $results = $response->json();
                if (count($results) > 0) {
                    $result = $results[0];
                    echo "Hasil API: " . json_encode($result) . "\n";
            
                    $desa->latitude = $result['lat'] ?? null;
                    $desa->longitude = $result['lon'] ?? null;
                    $desa->save();
            
                    echo "Koordinat untuk {$desa->nama} berhasil diperbarui.\n";
                } else {
                    echo "Tidak ada hasil untuk query: {$query}\n";
                }
            } else {
                echo "API gagal untuk query: {$query}. Status code: {$response->status()}\n";
            }
            
        }
    }
}
