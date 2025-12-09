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
            // Ubah kolom 'type' menjadi VARCHAR(50)
            $table->string('type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Kembalikan ke panjang semula (misalnya 10), ganti angka 10 sesuai kondisi awal Anda
            $table->string('type', 10)->change();
        });
    }
};
