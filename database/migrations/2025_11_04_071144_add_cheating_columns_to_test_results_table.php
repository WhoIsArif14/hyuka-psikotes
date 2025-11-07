<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->boolean('is_cheating')->default(false)->after('score');
            $table->text('cheating_notes')->nullable()->after('is_cheating');
        });
    }

    public function down()
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['is_cheating', 'cheating_notes']);
        });
    }
};