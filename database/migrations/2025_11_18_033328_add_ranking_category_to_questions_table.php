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
            // Tambah kolom untuk kategori perangkingan
            $table->string('ranking_category')->nullable();
            
            // Tambah kolom untuk bobot/poin soal (opsional, untuk weighted scoring)
            $table->integer('ranking_weight')->default(1)->after('ranking_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['ranking_category', 'ranking_weight']);
        });
    }
};