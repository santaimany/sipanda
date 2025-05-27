<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('harga_histori', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_pangan_id');
            $table->decimal('harga', 10, 2);
            $table->timestamps();
        
            $table->foreign('jenis_pangan_id')->references('id')->on('jenis_pangan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_histori');
    }
};
