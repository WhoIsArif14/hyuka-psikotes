<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat_tes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('deskripsi')->nullable();
            $table->integer('durasi')->default(0)->comment('durasi dalam menit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat_tes');
    }
};
