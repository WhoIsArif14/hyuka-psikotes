<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('papi_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('alat_tes_id')->nullable()->after('id');
            $table->foreign('alat_tes_id')
                  ->references('id')
                  ->on('alat_tes')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('papi_questions', function (Blueprint $table) {
            $table->dropForeign(['alat_tes_id']);
            $table->dropColumn('alat_tes_id');
        });
    }
};