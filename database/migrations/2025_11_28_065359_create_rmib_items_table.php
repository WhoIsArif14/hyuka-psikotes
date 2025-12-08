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
        Schema::create('rmib_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_number')->unique(); // 1-144
            $table->text('description'); // Deskripsi pekerjaan/aktivitas
            $table->string('interest_area', 50); // OUTDOOR, MECHANICAL, dll
            $table->string('version', 10)->default('1995'); // Versi RMIB
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('item_number');
            $table->index('interest_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rmib_items');
    }
};