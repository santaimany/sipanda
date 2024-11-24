<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';

    protected $fillable = [
        'desa_asal_id',
        'desa_tujuan_id',
        'jenis_pangan',
        'berat',
        'jarak',
        'status',
        'bapanas_id',
        'total_harga',
        'ongkir',
        'alasan',
    ];

    public function desaAsal()
    {
        return $this->belongsTo(Desa::class, 'desa_asal_id');
    }

    public function desaTujuan()
    {
        return $this->belongsTo(Desa::class, 'desa_tujuan_id');
    }

    public function bapanas()
    {
        return $this->belongsTo(User::class, 'bapanas_id');
    }
}
