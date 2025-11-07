<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cheating_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->nullable()->constrained('alat_tes')->onDelete('cascade');
            $table->foreignId('test_result_id')->nullable()->constrained('test_results')->onDelete('cascade');
            $table->enum('violation_type', [
                'TAB_SWITCH',
                'SCREENSHOT',
                'COPY_PASTE',
                'RIGHT_CLICK',
                'DEVELOPER_TOOLS',
                'WINDOW_BLUR',
                'FULLSCREEN_EXIT'
            ]);
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('violation_count')->default(1);
            $table->timestamp('detected_at');
            $table->timestamps();

            $table->index(['user_id', 'test_id', 'violation_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cheating_logs');
    }
};