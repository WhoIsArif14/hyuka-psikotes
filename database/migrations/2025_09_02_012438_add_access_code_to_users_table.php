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
            // Menambahkan kolom-kolom baru
            $table->string('access_code', 50)->unique()->nullable()->after('password');
            $table->enum('code_status', ['unused', 'used', 'expired'])->default('unused')->after('access_code');
            $table->timestamp('activated_at')->nullable()->after('code_status');

            // Mengubah kolom yang sudah ada agar bisa NULL
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom yang ditambahkan di method up()
            $table->dropColumn(['access_code', 'code_status', 'activated_at']);

            // Mengembalikan kolom email dan password menjadi NOT NULL (jika sebelumnya begitu)
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};