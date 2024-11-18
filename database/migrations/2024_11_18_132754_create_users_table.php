<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name', 100); // Nama
            $table->string('email', 100)->unique(); // Email
            $table->string('password', 255); // Password
            $table->enum('role', ['admin', 'kepala_desa', 'bapanas']); // Role
            $table->string('phone_number', 15)->nullable(); // Nomor telepon
            $table->unsignedBigInteger('desa_id')->nullable(); // Desa
            $table->unsignedBigInteger('province_id')->nullable(); // Provinsi
            $table->unsignedBigInteger('regency_id')->nullable(); // Kabupaten
            $table->unsignedBigInteger('district_id')->nullable(); // Kecamatan
            $table->unsignedBigInteger('village_id')->nullable(); // Desa/Kelurahan
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending'); // Status
            $table->string('qr_code', 255)->nullable(); // QR Code
            $table->timestamps();
            

            // Foreign Key
            $table->foreign('desa_id')->references('id')->on('desa')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}