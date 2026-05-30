<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {

            $table->id(); 

            $table->unsignedBigInteger('karyawan_id');

            $table->enum('type', ['makan', 'tunai']);

            $table->integer('total');

            $table->text('keterangan')->nullable();

            $table->date('tanggal');

            $table->string('status')->default('approved');

            $table->timestamps();

            $table->foreign('karyawan_id')
                ->references('id_kry')
                ->on('karyawans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};