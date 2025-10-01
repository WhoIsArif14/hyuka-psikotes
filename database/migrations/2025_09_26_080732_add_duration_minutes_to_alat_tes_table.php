<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->integer('duration_minutes')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
