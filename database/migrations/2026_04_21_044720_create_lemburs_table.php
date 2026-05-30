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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->id('id_lbr');
            $table->unsignedBigInteger('id_kry');
            // Hubungkan ke tabel karyawans pada kolom id_kry
            $table->foreign('id_kry')
                ->references('id_kry') // Nama kolom di tabel karyawans
                ->on('karyawans')      // Nama tabel referensi
                ->onDelete('cascade');
            $table->date('tgl_lbr');
            $table->decimal('qty_jam', 4, 1); // Menggunakan decimal agar mendukung 0.5 jam
            $table->text('keterangan'); 
            $table->string('bukti_foto')->nullable();
            $table->enum('sts_lbr', ['disetujui', 'menunggu', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
