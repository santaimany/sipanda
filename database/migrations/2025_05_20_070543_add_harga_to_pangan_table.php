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
        Schema::table('pangan', function (Blueprint $table) {
            // contoh: tipe decimal, 10 digit total dan 2 digit di belakang koma
            $table->decimal('harga', 10, 2)
                ->after('berat')    // letakkan kolom setelah kolom 'berat'
                ->default(0);       // bisa ditentukan default value jika perlu
        });
    }

    public function down()
    {
        Schema::table('pangan', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
};
