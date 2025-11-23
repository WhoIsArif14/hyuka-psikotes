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
        Schema::create('pauli_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->onDelete('cascade');
            $table->integer('total_columns')->default(45); // Jumlah kolom
            $table->integer('pairs_per_column')->default(45); // Pasangan angka per kolom
            $table->integer('time_per_column')->default(60); // Detik per kolom
            $table->timestamps();
        });

        Schema::create('pauli_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pauli_test_id')->constrained()->onDelete('cascade');
            $table->json('test_data'); // Angka yang dihasilkan
            $table->json('answers'); // Jawaban peserta
            $table->json('column_performance'); // Performa per kolom
            $table->integer('total_answers')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->decimal('accuracy', 5, 2)->default(0);
            $table->decimal('average_speed', 5, 2)->default(0);
            $table->integer('completion_time')->nullable(); // Total waktu dalam detik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pauli_results');
        Schema::dropIfExists('pauli_tests');
    }
};