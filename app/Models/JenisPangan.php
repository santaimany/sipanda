<?php
<<<<<<< HEAD
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> 190bc93 (insert pangan pakai enum ambil di data pangan pada tabel jenis_pangan)

class JenisPangan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pangan';

    protected $fillable = ['nama_pangan', 'harga'];
}
<<<<<<< HEAD

=======
>>>>>>> 190bc93 (insert pangan pakai enum ambil di data pangan pada tabel jenis_pangan)
