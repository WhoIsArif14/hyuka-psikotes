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
        Schema::create('memory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->onDelete('cascade');
            $table->text('content'); 
            $table->enum('type', ['TEXT', 'IMAGE'])->default('TEXT'); 
            $table->unsignedSmallInteger('duration_seconds')->default(10); 
            $table->unsignedSmallInteger('order'); 
            $table->timestamps();
            $table->unique(['alat_tes_id', 'order']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memory_items');
    }
};
