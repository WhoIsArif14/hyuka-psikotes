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
        Schema::table('alat_tes', function (Blueprint $table) {
            // Menambahkan kolom 'instructions' sebagai TEXT dan boleh NULL
            // MENGHILANGKAN ->after('description') untuk menghindari error jika kolom 'description' tidak ada.
            $table->text('instructions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            // Menghapus kolom 'instructions' saat rollback
            $table->dropColumn('instructions');
        });
    }
};