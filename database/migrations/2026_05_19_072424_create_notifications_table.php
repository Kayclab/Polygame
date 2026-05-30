<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');

            $table->unsignedBigInteger('id_kry');

            $table->string('title');
            $table->text('message');

            $table->string('type')->nullable();
            $table->string('priority')->default('medium');

            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->foreign('id_kry')
                ->references('id_kry')
                ->on('karyawans')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};