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
        Schema::table('questions', function (Blueprint $table) {
            // 1. Drop foreign key constraint yang lama
            $table->dropForeign(['test_id']);
            
            // 2. Tambah foreign key baru yang merujuk ke tabel 'alat_tes'
            $table->foreign('test_id')
                  ->references('id')
                  ->on('alat_tes')  // <-- Ganti dari 'tests' ke 'alat_tes'
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Kembalikan ke foreign key lama (tests)
            $table->dropForeign(['test_id']);
            
            $table->foreign('test_id')
                  ->references('id')
                  ->on('tests')
                  ->onDelete('cascade');
        });
    }
};