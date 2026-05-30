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
        Schema::table('soal_evaluasis', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('pertanyaan');
            
            $table->string('target_role')->default('staff')->after('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal_evaluasis', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'target_role']);
        });
    }
};
