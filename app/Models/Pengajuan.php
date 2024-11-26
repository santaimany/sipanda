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
        'total_harga',
        'ongkir',
        'pajak', // Tambahkan pajak
        'jasa_pengiriman',
        'status',
        'invoice_number',
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
