<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisPanganTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jenis_pangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pangan');
            $table->decimal('harga', 12, 2)
                  ->comment('Harga saat ini untuk jenis pangan ini');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pangan');
    }
}
