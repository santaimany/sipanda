<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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
    ];

    public function kepalaDesa()
    {
        return $this->belongsTo(User::class, 'kepala_desa_id');
    }

    public function pangan()
    {
        return $this->hasMany(Pangan::class, 'desa_id');
    }

    protected static function booted()
    {
        static::creating(function ($desa) {
            if (is_null($desa->latitude) || is_null($desa->longitude)) {
                $query = "{$desa->kelurahan}, {$desa->kecamatan}, {$desa->kabupaten}, {$desa->provinsi}";

                $response = Http::get("https://nominatim.openstreetmap.org/search", [
                    'q' => $query,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 1,
                ]);

                if ($response->successful() && count($response->json()) > 0) {
                    $result = $response->json()[0];
                    $desa->latitude = $result['lat'] ?? null;
                    $desa->longitude = $result['lon'] ?? null;
                }
            }
        });
    }
}
