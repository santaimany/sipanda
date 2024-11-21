
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
            $table->id();
            $table->string('nama', 100);
            $table->string('provinsi', 100);
            $table->string('kabupaten', 100);
            $table->string('kecamatan', 100);
            $table->string('kelurahan', 100);
            $table->unsignedBigInteger('kepala_desa_id')->nullable();
            $table->timestamps();
    
            // Foreign key constraint
            $table->foreign('kepala_desa_id')->references('id')->on('users')->onDelete('cascade');
    
        });
    }

    public function down()
    {
        Schema::dropIfExists('desa');
        Schema::disableForeignKeyConstraints();
    }
}
