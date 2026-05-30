<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slip_gajis', function (Blueprint $table) {

            $table->id('id_slip');

            $table->unsignedBigInteger('id_kry');

            $table->foreign('id_kry')
                ->references('id_kry')
                ->on('karyawans')
                ->onDelete('cascade');

            $table->string('periode');

            $table->decimal('gaji_pokok', 12, 0);

            $table->decimal('bonus', 12, 0)
                ->default(0);

            $table->decimal('potongan', 12, 0)
                ->default(0);

            $table->decimal('total_gaji', 12, 0);

            $table->string('file_slip')->nullable();
            
            $table->enum('status', [
                'draft',
                'terkirim'
            ])->default('terkirim');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slip_gajis');
    }
};
