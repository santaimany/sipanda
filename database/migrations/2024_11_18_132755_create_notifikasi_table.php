<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifikasiTable extends Migration
{
    public function up()
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('user_id'); // Referensi ke pengguna
            $table->text('message'); // Pesan notifikasi
            $table->enum('status', ['unread', 'read'])->default('unread'); // Status notifikasi
            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

  
    }

    public function down()
    {
        Schema::dropIfExists('notifikasi');
    }
}
