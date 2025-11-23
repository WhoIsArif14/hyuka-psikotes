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
        Schema::create('rmib_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->onDelete('cascade');
            $table->integer('item_number')->unique(); // 1-9 (untuk 9 kelompok)
            $table->string('group_title'); // Misal: "Kelompok 1"
            
            // 12 pernyataan per kelompok (A-L)
            $table->text('statement_a'); // Outdoor (O)
            $table->text('statement_b'); // Mechanical (M)
            $table->text('statement_c'); // Computational (C)
            $table->text('statement_d'); // Scientific (S)
            $table->text('statement_e'); // Personal Contact (P)
            $table->text('statement_f'); // Aesthetic (A)
            $table->text('statement_g'); // Literary (L)
            $table->text('statement_h'); // Musical (Mu)
            $table->text('statement_i'); // Social Service (SS)
            $table->text('statement_j'); // Clerical (Cl)
            $table->text('statement_k'); // Practical (Pr)
            $table->text('statement_l'); // Medical (Me)
            
            // Kunci jawaban (kategori minat untuk setiap statement)
            $table->char('key_a', 2); // O, M, C, S, P, A, L, Mu, SS, Cl, Pr, Me
            $table->char('key_b', 2);
            $table->char('key_c', 2);
            $table->char('key_d', 2);
            $table->char('key_e', 2);
            $table->char('key_f', 2);
            $table->char('key_g', 2);
            $table->char('key_h', 2);
            $table->char('key_i', 2);
            $table->char('key_j', 2);
            $table->char('key_k', 2);
            $table->char('key_l', 2);
            
            $table->timestamps();
        });

        // Tabel untuk menyimpan hasil jawaban RMIB
        Schema::create('rmib_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->onDelete('cascade');
            $table->foreignId('rmib_question_id')->constrained('rmib_questions')->onDelete('cascade');
            
            // Jawaban user: ranking 1-12 untuk setiap statement
            $table->integer('rank_a')->nullable(); // 1 = paling disukai, 12 = paling tidak disukai
            $table->integer('rank_b')->nullable();
            $table->integer('rank_c')->nullable();
            $table->integer('rank_d')->nullable();
            $table->integer('rank_e')->nullable();
            $table->integer('rank_f')->nullable();
            $table->integer('rank_g')->nullable();
            $table->integer('rank_h')->nullable();
            $table->integer('rank_i')->nullable();
            $table->integer('rank_j')->nullable();
            $table->integer('rank_k')->nullable();
            $table->integer('rank_l')->nullable();
            
            $table->timestamps();
            
            // Unique constraint: 1 jawaban per user per question
            $table->unique(['user_id', 'rmib_question_id']);
        });

        // Tabel untuk hasil skor RMIB per kategori
        Schema::create('rmib_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->onDelete('cascade');
            
            // Skor untuk 12 kategori minat (akumulasi dari 9 kelompok)
            $table->integer('score_outdoor')->default(0);        // O
            $table->integer('score_mechanical')->default(0);     // M
            $table->integer('score_computational')->default(0);  // C
            $table->integer('score_scientific')->default(0);     // S
            $table->integer('score_personal')->default(0);       // P
            $table->integer('score_aesthetic')->default(0);      // A
            $table->integer('score_literary')->default(0);       // L
            $table->integer('score_musical')->default(0);        // Mu
            $table->integer('score_social')->default(0);         // SS
            $table->integer('score_clerical')->default(0);       // Cl
            $table->integer('score_practical')->default(0);      // Pr
            $table->integer('score_medical')->default(0);        // Me
            
            // Ranking kategori (1-12, 1 = minat tertinggi)
            $table->json('interest_ranking')->nullable();
            
            // Top 3 minat
            $table->string('top_interest_1')->nullable();
            $table->string('top_interest_2')->nullable();
            $table->string('top_interest_3')->nullable();
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'alat_tes_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rmib_results');
        Schema::dropIfExists('rmib_answers');
        Schema::dropIfExists('rmib_questions');
    }
};