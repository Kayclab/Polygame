<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluasis', function (Blueprint $table) {
            $table->id('id_evl');
            $table->date('tgl_evl'); // Sesuai atribut di ERD
            $table->string('periode', 7); // Tambahan ringkas untuk filter, contoh: '2026-05'
            
            // Siapa karyawan yang DINILAI (Sesuai foreign key ERD ke tabel Karyawan)
            $table->unsignedBigInteger('id_kry');
            $table->foreign('id_kry')->references('id_kry')->on('karyawans')->onDelete('cascade');

            // Siapa karyawan yang MENILAI (Penilai)
            $table->unsignedBigInteger('id_penilai');
            $table->foreign('id_penilai')->references('id_kry')->on('karyawans')->onDelete('cascade');

            $table->integer('skor_total')->nullable(); // Sesuai atribut di ERD
            $table->string('rating', 10)->nullable(); // Sesuai atribut di ERD (Misal: A, B, C)
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};
