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
        Schema::table('options', function (Blueprint $table) {
            // Menambahkan kolom untuk path gambar setelah kolom 'option_text'
            // Dibuat nullable karena tidak semua pilihan jawaban memiliki gambar.
            $table->string('image_path')->nullable()->after('option_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn('image_path');
        });
    }
};

