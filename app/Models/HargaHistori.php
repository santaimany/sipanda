<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HargaHistori extends Model
{
    use HasFactory;

    protected $table = 'harga_histori';

    protected $fillable = [
        'jenis_pangan_id',
        'harga',
    ];

    public function jenisPangan()
    {
        return $this->belongsTo(JenisPangan::class, 'jenis_pangan_id');
    }
}
