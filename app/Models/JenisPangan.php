<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPangan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pangan';

    protected $fillable = ['nama_pangan', 'harga'];

    // Relasi ke histori harga
    public function hargaHistori()
    {
        return $this->hasMany(HargaHistori::class, 'jenis_pangan_id');
    }
}
