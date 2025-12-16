<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rmib_answers', function (Blueprint $table) {
            // Cek apakah kolom 'rating' ada, jika ada rename ke 'ranking'
            if (Schema::hasColumn('rmib_answers', 'rating')) {
                $table->renameColumn('rating', 'ranking');
            } 
            // Jika tidak ada kolom rating, tambahkan kolom ranking
            elseif (!Schema::hasColumn('rmib_answers', 'ranking')) {
                // Tambahkan setelah kolom ID (bukan after rmib_item_id)
                $table->integer('ranking')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rmib_answers', function (Blueprint $table) {
            if (Schema::hasColumn('rmib_answers', 'ranking')) {
                $table->renameColumn('ranking', 'rating');
            }
        });
    }
};