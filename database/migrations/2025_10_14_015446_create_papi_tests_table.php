<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Perbaikan Tabel PAPI QUESTIONS (Soal)
        Schema::create('papi_questions', function (Blueprint $table) {
            $table->id();
            // Menggunakan unsignedSmallInteger dan unique untuk nomor soal (1-90)
            $table->unsignedSmallInteger('item_number')->unique(); 
            $table->text('statement_a');
            $table->text('statement_b'); 
            
            // KOLOM KRUSIAL: Memisahkan 4 Aspek (Role dan Need)
            $table->string('role_a', 1); // Kode Role A (G, L, I, T, V, S, R, D, C, E)
            $table->string('need_a', 1); // Kode Need A (N, A, P, X, B, O, Z, K, F, W)
            $table->string('role_b', 1);
            $table->string('need_b', 1);
            
            $table->timestamps();
        });

        // 2. Perbaikan Tabel PAPI RESULTS (Hasil)
        Schema::create('papi_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            // Menambahkan unique() untuk memastikan user hanya memiliki 1 hasil PAPI
            $table->unique('user_id'); 

            // Metadata
            $table->string('participant_name')->nullable();
            $table->string('participant_number')->nullable();
            $table->date('test_date')->nullable();
            $table->date('check_date')->nullable();
            $table->json('answers')->nullable(); // Tetap simpan jawaban mentah
            $table->text('interpretation')->nullable();
            $table->dateTime('completed_at')->nullable();

            // 20 KOLOM SKOR MENTAH (MENGGANTIKAN KOLOM 'scores' JSON)
            $table->unsignedTinyInteger('G')->default(0); 
            $table->unsignedTinyInteger('L')->default(0); 
            $table->unsignedTinyInteger('I')->default(0); 
            $table->unsignedTinyInteger('T')->default(0); 
            $table->unsignedTinyInteger('V')->default(0); 
            $table->unsignedTinyInteger('S')->default(0); 
            $table->unsignedTinyInteger('R')->default(0); 
            $table->unsignedTinyInteger('D')->default(0); 
            $table->unsignedTinyInteger('C')->default(0); 
            $table->unsignedTinyInteger('E')->default(0); 
            $table->unsignedTinyInteger('N')->default(0); 
            $table->unsignedTinyInteger('A')->default(0); 
            $table->unsignedTinyInteger('P')->default(0); 
            $table->unsignedTinyInteger('X')->default(0); 
            $table->unsignedTinyInteger('B')->default(0); 
            $table->unsignedTinyInteger('O')->default(0); 
            $table->unsignedTinyInteger('Z')->default(0); 
            $table->unsignedTinyInteger('K')->default(0); 
            $table->unsignedTinyInteger('F')->default(0); 
            $table->unsignedTinyInteger('W')->default(0); 
            
            $table->timestamps();
            
            // Hapus $table->json('scores') dan $table->integer('total_score')
            // karena sudah digantikan oleh 20 kolom skor di atas.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papi_results');
        Schema::dropIfExists('papi_questions');
    }
};