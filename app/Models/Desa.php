<?php
namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Desa extends Model
{
    use HasFactory;
    protected $table = 'desa';
    protected $fillable = [
        'nama',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'kepala_desa_id',
        'latitude',
        'longitude'
    ];

    public function kepalaDesa()
    {
        return $this->belongsTo(User::class, 'kepala_desa_id');
    }

    public function pangan()
    {
        return $this->hasMany(Pangan::class, 'desa_id');
    }

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($desa) {
            if (is_null($desa->latitude) || is_null($desa->longitude)) {
                $query = "{$desa->nama}, Indonesia";
    
                try {
                    Log::info("Mencoba mencari koordinat untuk Desa: {$query}");
                    
                    $response = Http::withHeaders([
                        'User-Agent' => 'DesaLocator/1.0 (example@example.com)',
                    ])->get("https://nominatim.openstreetmap.org/search", [
                        'q' => $query,
                        'format' => 'json',
                        'addressdetails' => 1,
                        'limit' => 1,
                    ]);
    
                    if ($response->successful() && count($response->json()) > 0) {
                        $result = $response->json()[0];
                        Log::info("Koordinat ditemukan untuk Desa: {$query}, Latitude: {$result['lat']}, Longitude: {$result['lon']}");
                        $desa->latitude = $result['lat'];
                        $desa->longitude = $result['lon'];
                    } else {
                        Log::warning("Tidak ada hasil untuk query: {$query}. Respons: " . $response->body());
                        $desa->latitude = null;
                        $desa->longitude = null;
                    }
                } catch (\Exception $e) {
                    Log::error("Gagal mendapatkan koordinat untuk Desa: {$query}. Error: {$e->getMessage()}");
                    $desa->latitude = null;
                    $desa->longitude = null;
                }
            }
        });
    }
    
}
