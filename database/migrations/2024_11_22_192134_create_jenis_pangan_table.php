<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('data_pangan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pangan_id');
            $table->string('nama');
            $table->decimal('harga', 10, 2);
            $table->timestamps();

            // Menambahkan foreign key untuk menghubungkan ke tabel pangan
            $table->foreign('pangan_id')->references('id')->on('pangan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_pangan');
    }
};
