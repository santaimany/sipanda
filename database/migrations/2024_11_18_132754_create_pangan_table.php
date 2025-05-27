
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanganTable extends Migration
{
    public function up()
    {
        Schema::create('pangan', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('desa_id'); // Desa pemilik pangan
            $table->string('jenis_pangan', 100); // Jenis pangan
            $table->decimal('berat', 10, 2); // Berat pangan
            $table->decimal('harga', 10, 2); // Harga pangan
            $table->timestamp('tanggal'); // Tanggal pencatatan
            $table->timestamps();

            // Foreign Key
            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pangan');
    }
}
