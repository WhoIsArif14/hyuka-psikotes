<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ✅ DENGAN PENGECEKAN - Aman untuk jalankan berkali-kali
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // ✅ CEK DULU: Apakah kolom sudah ada?
            if (!Schema::hasColumn('questions', 'correct_answers')) {
                $table->json('correct_answers')
                      ->nullable()
                      ->comment('Array of correct answer indices for PILIHAN_GANDA_KOMPLEKS')
                      ->after('correct_answer_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // ✅ CEK DULU: Sebelum dihapus
            if (Schema::hasColumn('questions', 'correct_answers')) {
                $table->dropColumn('correct_answers');
            }
        });
    }
};