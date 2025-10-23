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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom string untuk kode aktivasi peserta.
            // Kolom ini bersifat UNIK (kode tidak boleh sama antar peserta)
            // dan boleh NULL (jika beberapa user lama tidak punya kode).
            $table->string('kode_aktivasi_peserta', 10)
                  ->nullable()
                  ->unique()
                  ->after('password'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn('kode_aktivasi_peserta');
        });
    }
};