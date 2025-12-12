<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            if (!Schema::hasColumn('alat_tes', 'example_questions')) {
                $table->json('example_questions')->nullable()->after('instructions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            if (Schema::hasColumn('alat_tes', 'example_questions')) {
                $table->dropColumn('example_questions');
            }
        });
    }
};