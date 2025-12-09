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
            // Memperluas kolom 'type' menjadi 50 karakter untuk menampung 'PAPIKOSTICK'
            $table->string('type', 50)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Mengubah kembali, asumsikan sebelumnya 255 atau sesuai kebutuhan awal
            $table->string('type', 255)->change(); 
        });
    }
};