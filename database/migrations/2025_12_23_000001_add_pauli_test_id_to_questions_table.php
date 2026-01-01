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
        Schema::table('questions', function (Blueprint $table) {
            // Make test_id nullable (if supported by DB/driver)
            if (Schema::hasColumn('questions', 'test_id')) {
                $table->unsignedBigInteger('test_id')->nullable()->change();
            }

            // Add pauli_test_id to reference pauli_tests
            if (!Schema::hasColumn('questions', 'pauli_test_id')) {
                $table->foreignId('pauli_test_id')->nullable()->constrained('pauli_tests')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'pauli_test_id')) {
                $table->dropConstrainedForeignId('pauli_test_id');
            }

            // Revert test_id to non-nullable only if needed
            // Note: change() may require doctrine/dbal and may not be reversible safely here
        });
    }
};
