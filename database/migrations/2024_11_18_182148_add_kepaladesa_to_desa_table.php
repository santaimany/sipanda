<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKepalaDesaIdToDesaTable extends Migration
{
    public function up()
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->unsignedBigInteger('kepala_desa_id')->nullable()->after('kelurahan');
            $table->foreign('kepala_desa_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->dropForeign(['kepala_desa_id']);
            $table->dropColumn('kepala_desa_id');
        });
    }
}
