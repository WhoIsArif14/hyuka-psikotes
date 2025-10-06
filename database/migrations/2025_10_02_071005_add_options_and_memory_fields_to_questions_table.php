<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // 1. Ubah kolom type dari VARCHAR ke ENUM
            DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM('PILIHAN_GANDA', 'ESSAY', 'HAFALAN', 'multiple_choice') DEFAULT 'PILIHAN_GANDA'");
            
            // 2. Tambah kolom options untuk menyimpan pilihan jawaban (JSON)
            if (!Schema::hasColumn('questions', 'options')) {
                $table->json('options')->nullable()->after('image_path');
            }
            
            // 3. Tambah kolom correct_answer_index untuk jawaban benar
            if (!Schema::hasColumn('questions', 'correct_answer_index')) {
                $table->integer('correct_answer_index')->nullable()->after('options');
            }
            
            // 4. Tambah kolom memory_content untuk konten hafalan
            if (!Schema::hasColumn('questions', 'memory_content')) {
                $table->text('memory_content')->nullable()->after('correct_answer_index');
            }
            
            // 5. Tambah kolom memory_type untuk tipe konten hafalan
            if (!Schema::hasColumn('questions', 'memory_type')) {
                $table->enum('memory_type', ['TEXT', 'IMAGE'])->nullable()->after('memory_content');
            }
            
            // 6. Tambah kolom duration_seconds untuk durasi tampil hafalan
            if (!Schema::hasColumn('questions', 'duration_seconds')) {
                $table->integer('duration_seconds')->nullable()->after('memory_type');
            }
        });

        // Update data lama: ubah 'multiple_choice' menjadi 'PILIHAN_GANDA'
        DB::table('questions')
            ->where('type', 'multiple_choice')
            ->update(['type' => 'PILIHAN_GANDA']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Drop kolom yang ditambahkan
            if (Schema::hasColumn('questions', 'options')) {
                $table->dropColumn('options');
            }
            if (Schema::hasColumn('questions', 'correct_answer_index')) {
                $table->dropColumn('correct_answer_index');
            }
            if (Schema::hasColumn('questions', 'memory_content')) {
                $table->dropColumn('memory_content');
            }
            if (Schema::hasColumn('questions', 'memory_type')) {
                $table->dropColumn('memory_type');
            }
            if (Schema::hasColumn('questions', 'duration_seconds')) {
                $table->dropColumn('duration_seconds');
            }
        });

        // Kembalikan type ke VARCHAR
        DB::statement("ALTER TABLE questions MODIFY COLUMN type VARCHAR(255) DEFAULT 'multiple_choice'");
    }
};