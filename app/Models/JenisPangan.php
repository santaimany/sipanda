<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> pengajuan-kepaladesa
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> 190bc93 (insert pangan pakai enum ambil di data pangan pada tabel jenis_pangan)
=======
>>>>>>> pengajuan-kepaladesa

class JenisPangan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pangan';

    protected $fillable = ['nama_pangan', 'harga'];
}
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 190bc93 (insert pangan pakai enum ambil di data pangan pada tabel jenis_pangan)
=======

>>>>>>> pengajuan-kepaladesa
