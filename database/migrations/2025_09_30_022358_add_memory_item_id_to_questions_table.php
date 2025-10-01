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
            // Menambahkan foreign key yang nullable (karena tidak semua soal adalah soal memori/recall)
            $table->foreignId('memory_item_id')
                  ->nullable()
                  ->after('alat_tes_id')
                  ->constrained('memory_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Menghapus foreign key dan kolom jika rollback
            $table->dropConstrainedForeignId('memory_item_id');
        });
    }
};
