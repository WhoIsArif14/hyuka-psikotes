<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('personality_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('personality_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personality_test_id')->constrained('personality_tests')->cascadeOnDelete();
            $table->text('question');
            $table->json('options')->nullable(); // array of {label, value}
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('personality_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personality_test_id')->constrained('personality_tests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('score');
            $table->string('interpretation');
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personality_results');
        Schema::dropIfExists('personality_questions');
        Schema::dropIfExists('personality_tests');
    }
};
