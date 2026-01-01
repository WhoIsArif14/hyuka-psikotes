<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('progress_pengerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->nullable()->constrained('tests')->onDelete('cascade');
            $table->foreignId('alat_tes_id')->nullable()->constrained('alat_tes')->onDelete('cascade');
            $table->unsignedBigInteger('modul_terakhir_id')->nullable();
            $table->string('current_module')->nullable();
            $table->unsignedTinyInteger('percentage')->default(0);
            $table->string('status')->default('On Progress'); // On Progress, Completed
            $table->timestamps();

            $table->index(['user_id', 'test_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('progress_pengerjaan');
    }
};