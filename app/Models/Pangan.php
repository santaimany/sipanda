<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangan extends Model
{
    use HasFactory;

    protected $table='pangan';
    protected $fillable = [
        'desa_id',
        'jenis_pangan',
        'berat',
        'harga',
        'tanggal',
    ];
}
