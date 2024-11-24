<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaOngkirToPengajuanTable extends Migration
{
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->decimal('total_harga', 12, 2)->after('berat')->nullable(); // Total harga
            $table->decimal('ongkir', 12, 2)->after('total_harga')->nullable(); // Ongkos kirim
            $table->text('alasan')->after('status')->nullable(); // Alasan jika ditolak
        });
    }

    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn(['total_harga', 'ongkir', 'alasan']);
        });
    }
}
