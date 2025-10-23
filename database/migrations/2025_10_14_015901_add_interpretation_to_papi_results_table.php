<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('papi_results', function (Blueprint $table) {
            // 1. Drop kolom lama jika ada
            if (Schema::hasColumn('papi_results', 'scores')) {
                $table->dropColumn('scores');
            }

            if (Schema::hasColumn('papi_results', 'total_score')) {
                $table->dropColumn('total_score');
            }

            // 2. Tambahkan kolom baru hanya jika belum ada
            $columns = ['G','L','I','T','V','S','R','D','C','E','N','A','P','X','B','O','Z','K','F','W'];

            foreach ($columns as $col) {
                if (!Schema::hasColumn('papi_results', $col)) {
                    $table->unsignedTinyInteger($col)->default(0)->after('answers');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('papi_results', function (Blueprint $table) {
            $columns = ['G','L','I','T','V','S','R','D','C','E','N','A','P','X','B','O','Z','K','F','W'];

            foreach ($columns as $col) {
                if (Schema::hasColumn('papi_results', $col)) {
                    $table->dropColumn($col);
                }
            }

            if (!Schema::hasColumn('papi_results', 'scores')) {
                $table->json('scores')->nullable();
            }

            if (!Schema::hasColumn('papi_results', 'total_score')) {
                $table->integer('total_score')->default(0);
            }
        });
    }
};
