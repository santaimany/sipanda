<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
