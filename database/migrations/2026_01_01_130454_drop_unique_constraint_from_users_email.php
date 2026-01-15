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
        Schema::table('users', function (Blueprint $table) {
            // Drop unique constraint dari kolom email
            // Karena satu peserta bisa mengikuti beberapa test dengan kode aktivasi berbeda
            $table->dropUnique('users_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan unique constraint jika rollback
            $table->unique('email');
        });
    }
};
