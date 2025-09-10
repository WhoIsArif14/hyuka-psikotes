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
        Schema::table('test_results', function (Blueprint $table) {
            // Menambahkan kolom-kolom baru setelah participant_name
            $table->string('participant_email')->nullable()->after('participant_name');
            $table->string('education')->nullable()->after('participant_email'); // S1, D3, SMA, dll.
            $table->string('major')->nullable()->after('education'); // Jurusan
            $table->string('phone_number')->nullable()->after('major');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['participant_email', 'education', 'major', 'phone_number']);
        });
    }
};

