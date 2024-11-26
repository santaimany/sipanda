<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\After;

class UpdateInvoiceNumberToPengajuanTable extends Migration
{
    public function up()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->string('invoice_number')->after('bapanas_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }
}
