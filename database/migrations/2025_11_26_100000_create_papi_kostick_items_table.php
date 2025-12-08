<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up()
       {
           Schema::create('papi_kostick_items', function (Blueprint $table) {
               $table->id();
               $table->string('code')->unique(); // contoh: A, B, C, dll
               $table->text('description')->nullable();
               $table->timestamps();
           });
       }

       public function down()
       {
           Schema::dropIfExists('papi_kostick_items');
       }
   };