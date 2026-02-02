<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->unsignedBigInteger('question_id'); 
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnUpdate()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('question_id'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};


