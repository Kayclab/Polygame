<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Symfony\Component\Clock\now;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id('id_kry');
            $table->string('n_kry');
            $table->enum('jab', ['Owner', 'Barista', 'Game Master'])->default('Barista');
            $table->string('alamat')->nullable();
            $table->string('tmpt_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->date('tgl_mulai_kerja')->nullable();
            $table->string('telp')->nullable();
            $table->string('email');
            $table->string('password');
            $table->enum('role', ['owner', 'staff'])->default('staff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
