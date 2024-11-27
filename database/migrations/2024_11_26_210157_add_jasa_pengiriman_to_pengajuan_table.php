<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJasaPengirimanToPengajuanTable extends Migration
{
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->string('jasa_pengiriman')->after('pajak')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('jasa_pengiriman');
        });
    }
}
