<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('papi_questions', function (Blueprint $table) {
            // Mengubah kolom-kolom penskoran PAPI menjadi NULLABLE
            $table->string('role_a', 1)->nullable()->change();
            $table->string('need_a', 1)->nullable()->change();
            $table->string('role_b', 1)->nullable()->change();
            $table->string('need_b', 1)->nullable()->change();
        });
    }

    /**
     * Mengembalikan migrasi.
     */
    public function down(): void
    {
        Schema::table('papi_questions', function (Blueprint $table) {
            // Mengembalikan kolom-kolom ini menjadi NOT NULL
            // CATATAN: Ini hanya akan berfungsi jika kolom tersebut TIDAK ADA data NULL.
            $table->string('role_a', 1)->change();
            $table->string('need_a', 1)->change();
            $table->string('role_b', 1)->change();
            $table->string('need_b', 1)->change();
        });
    }
};
