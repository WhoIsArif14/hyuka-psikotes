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
        Schema::table('activation_codes', function (Blueprint $table) {
            // Kolom untuk menyimpan alamat IP pertama yang menggunakan kode ini
            $table->ipAddress()->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activation_codes', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
};

