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
        Schema::table('desa', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('kelurahan');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }
    
    public function down()
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
    
};
