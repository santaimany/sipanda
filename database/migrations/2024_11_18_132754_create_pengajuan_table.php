<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanTable extends Migration
{
    public function up()
    {
        

        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('desa_asal_id'); // Desa asal
            $table->unsignedBigInteger('desa_tujuan_id'); // Desa tujuan
            $table->string('jenis_pangan', 100); // Jenis pangan
            $table->decimal('berat', 10, 2); // Berat pangan
            $table->decimal('jarak', 10, 2); // Jarak pengiriman
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status
            $table->unsignedBigInteger('bapanas_id'); // Referensi ke Bapanas
            $table->timestamps();

            // Foreign Keys
            $table->foreign('desa_asal_id')->references('id')->on('desa')->onDelete('cascade');
            $table->foreign('desa_tujuan_id')->references('id')->on('desa')->onDelete('cascade');
            $table->foreign('bapanas_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan');
    }
}