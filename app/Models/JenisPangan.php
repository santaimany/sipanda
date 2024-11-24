<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPangan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pangan';

    protected $fillable = ['nama_pangan', 'harga'];
}

