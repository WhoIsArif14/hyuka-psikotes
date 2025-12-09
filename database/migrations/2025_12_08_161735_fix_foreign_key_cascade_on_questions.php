<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Drop foreign key lama
            $table->dropForeign(['alat_tes_id']);
            
            // Buat foreign key baru dengan CASCADE
            $table->foreign('alat_tes_id')
                  ->references('id')
                  ->on('alat_tes')
                  ->onDelete('cascade'); // ✅ Otomatis hapus soal
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['alat_tes_id']);
            
            // Kembalikan ke restrict (default)
            $table->foreign('alat_tes_id')
                  ->references('id')
                  ->on('alat_tes');
        });
    }
};