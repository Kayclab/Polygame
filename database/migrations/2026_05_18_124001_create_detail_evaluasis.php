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
        Schema::create('detail_evaluasis', function (Blueprint $table) {
            $table->id(); // Primary key internal laravel
    
            // Foreign key ke tabel Evaluasi (id_evl)
            $table->unsignedBigInteger('id_evl');
            $table->foreign('id_evl')->references('id_evl')->on('evaluasis')->onDelete('cascade');

            // Foreign key ke tabel Soal_Evaluasi (id_soal)
            $table->unsignedBigInteger('id_soal');
            $table->foreign('id_soal')->references('id_soal')->on('soal_evaluasis')->onDelete('cascade');

            $table->integer('jawaban'); // Sesuai atribut di ERD (Skor per nomor, misal 1-5)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_evaluasis');
    }
};
