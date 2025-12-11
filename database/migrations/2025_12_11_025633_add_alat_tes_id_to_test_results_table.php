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
            // Tambahkan kolom alat_tes_id dan buat foreign key
            $table->unsignedBigInteger('alat_tes_id')->after('user_id')->nullable();
            $table->foreign('alat_tes_id')->references('id')->on('alat_tes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            //
        });
    }
};
