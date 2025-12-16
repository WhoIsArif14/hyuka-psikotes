<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rmib_items', function (Blueprint $table) {
            $table->string('group_label', 5)->after('item_number'); // A, B, C, D, E, F
            $table->integer('position_in_group')->after('group_label'); // 1-12
        });
    }

    public function down(): void
    {
        Schema::table('rmib_items', function (Blueprint $table) {
            $table->dropColumn(['group_label', 'position_in_group']);
        });
    }
};