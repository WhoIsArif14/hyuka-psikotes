<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Hapus foreign key yang ada terlebih dahulu jika migration dijalankan ulang tanpa rollback
            // $table->dropForeign(['alat_tes_id']); 

            $table->unsignedBigInteger('alat_tes_id')->nullable();

            // --- BARIS PENTING YANG DIUBAH ---
            $table->foreign('alat_tes_id')
                ->references('id')
                ->on('alat_tes')
                ->onDelete('cascade'); // <-- Tambahkan ini!
            // ---------------------------------
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            //
        });
    }
};
