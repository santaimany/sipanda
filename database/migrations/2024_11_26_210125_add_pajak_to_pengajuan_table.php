<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPajakToPengajuanTable extends Migration
{
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->decimal('pajak', 15, 2)->default(0)->after('ongkir'); // Tambahkan kolom pajak
        });
    }

    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('pajak');
        });
    }
}
