<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('papi_kostick_item_id')->nullable()->after('alat_tes_id')->constrained('papi_kostick_items')->onDelete('set null');
            $table->json('metadata')->nullable()->after('ranking_weight');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['papi_kostick_item_id']);
            $table->dropColumn('papi_kostick_item_id');
            $table->dropColumn('metadata');
        });
    }
};