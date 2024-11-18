
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesaTable extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('desa', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('nama', 100); // Nama desa
            $table->string('provinsi', 100); // Nama provinsi
            $table->string('kabupaten', 100); // Nama kabupaten
            $table->string('kecamatan', 100); // Nama kecamatan
            $table->string('kelurahan', 100)->nullable(); // Nama kelurahan
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('desa');
    }
}
