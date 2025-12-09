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
            // Tambahkan kolom 'description' sebagai TEXT yang bisa NULL
            $table->text('description')->nullable()->after('instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            // Jika rollback, hapus kolom 'description'
            $table->dropColumn('description');
        });
    }
};