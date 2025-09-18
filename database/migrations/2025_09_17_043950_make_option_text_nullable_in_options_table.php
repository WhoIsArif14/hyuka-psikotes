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
            // Mengubah kolom 'option_text' agar bisa menerima nilai NULL
            $table->text('option_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula jika migrasi di-rollback
            $table->text('option_text')->nullable(false)->change();
        });
    }
};