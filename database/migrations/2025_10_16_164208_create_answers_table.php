<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->string('response', 45);
            $table->string('answer', 45);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('index_for_censo_question_id')->constrained('index_for_censo_question')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('answers');
    }
};


