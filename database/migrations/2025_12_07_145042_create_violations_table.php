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
        Schema::create('test_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('test_id');
            
            // Tipe pelanggaran
            $table->string('violation_type'); // screenshot_attempt, tab_switch, etc
            $table->text('details')->nullable();
            
            // Metadata
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('occurred_at');
            
            $table->timestamps();

            // Indexes untuk performance
            $table->index(['user_id', 'test_id']);
            $table->index('violation_type');
            $table->index('occurred_at');
            $table->index('created_at');
            
            // Foreign keys (jika tabel users dan tests ada)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_violations');
    }
};