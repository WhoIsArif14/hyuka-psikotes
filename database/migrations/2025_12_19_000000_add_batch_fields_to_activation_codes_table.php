<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activation_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('activation_codes', 'batch_code')) {
                $table->string('batch_code', 50)->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('activation_codes', 'batch_name')) {
                $table->string('batch_name')->nullable()->after('batch_code');
            }
            if (!Schema::hasColumn('activation_codes', 'status')) {
                $table->string('status')->default('Pending')->after('code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activation_codes', function (Blueprint $table) {
            if (Schema::hasColumn('activation_codes', 'batch_code')) {
                $table->dropColumn('batch_code');
            }
            if (Schema::hasColumn('activation_codes', 'batch_name')) {
                $table->dropColumn('batch_name');
            }
            if (Schema::hasColumn('activation_codes', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
