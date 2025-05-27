<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangan extends Model
{
    use HasFactory;

    protected $table = 'pangan';
    protected $fillable = [
        'desa_id', 'jenis_pangan', 'berat', 'harga','tanggal'
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
      
    }

    public function jenisPangan()
    {
        return $this->belongsTo(JenisPangan::class);
    }



    protected $dates = ['updated_at'];
}
