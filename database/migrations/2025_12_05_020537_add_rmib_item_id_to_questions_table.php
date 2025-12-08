<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('rmib_item_id')->nullable()->after('alat_tes_id');
            
            // Optional: Add foreign key jika tabel rmib_items ada
            $table->foreign('rmib_item_id')
                  ->references('id')
                  ->on('rmib_items')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['rmib_item_id']);
            $table->dropColumn('rmib_item_id');
        });
    }
};