<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Null jika notifikasi untuk semua user (e.g., admin atau Bapanas)
            $table->unsignedBigInteger('desa_id')->nullable(); // Null jika tidak terkait desa
            $table->string('title'); // Judul notifikasi
            $table->text('message'); // Isi pesan
            $table->string('type'); // Jenis notifikasi (e.g., pengajuan, approval, harga_update)
            $table->boolean('is_read')->default(false); // Status sudah dibaca atau belum
            $table->timestamps(); // Timestamps untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}
