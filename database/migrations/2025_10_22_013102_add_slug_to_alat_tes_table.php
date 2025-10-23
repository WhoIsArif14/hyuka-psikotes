<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('duration_minutes');
        });
    }

    public function down()
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
