<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ CARA PALING AMAN: Gunakan raw SQL
        try {
            DB::statement('ALTER TABLE user_answers DROP FOREIGN KEY user_answers_option_id_foreign');
        } catch (\Exception $e) {
            // Foreign key tidak ada, skip
        }

        Schema::table('user_answers', function (Blueprint $table) {
            // Drop kolom option_id lama jika ada
            if (Schema::hasColumn('user_answers', 'option_id')) {
                $table->dropColumn('option_id');
            }
        });

        Schema::table('user_answers', function (Blueprint $table) {
            // Tambah kolom baru tanpa foreign key
            if (!Schema::hasColumn('user_answers', 'option_id')) {
                $table->integer('option_id')->nullable()->after('question_id');
            }
            
            // Tambah answer_text jika belum ada
            if (!Schema::hasColumn('user_answers', 'answer_text')) {
                $table->text('answer_text')->nullable()->after('option_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_answers', function (Blueprint $table) {
            // Drop kolom yang ditambahkan di method up()
            if (Schema::hasColumn('user_answers', 'answer_text')) {
                $table->dropColumn('answer_text');
            }
            
            if (Schema::hasColumn('user_answers', 'option_id')) {
                $table->dropColumn('option_id');
            }
        });

        // Kembalikan ke struktur lama dengan foreign key (opsional)
        // Schema::table('user_answers', function (Blueprint $table) {
        //     $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade');
        // });
    }
};