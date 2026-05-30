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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('id_announcement');
            $table->string('title');
            $table->text('content');
            
            // UBAH: Hubungkan kolom author ke id_kry di tabel karyawans
            $table->unsignedBigInteger('id_kry');
            $table->foreign('id_kry')
                ->references('id_kry')
                ->on('karyawans')
                ->onDelete('cascade'); // Jika admin dihapus, pengumuman ikut terhapus (opsional)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
