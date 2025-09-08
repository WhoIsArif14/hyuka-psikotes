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
        Schema::table('test_results', function (Blueprint $table) {
            // Mengubah kolom user_id menjadi nullable, karena peserta tidak punya akun
            $table->foreignId('user_id')->nullable()->change();
            // Menambahkan kolom baru untuk menyimpan nama peserta
            $table->string('participant_name')->nullable()->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            // Kembalikan kolom user_id menjadi not nullable
            $table->foreignId('user_id')->nullable(false)->change();
            // Hapus kolom nama peserta
            $table->dropColumn('participant_name');
        });
    }
};

