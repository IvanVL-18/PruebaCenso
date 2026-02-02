<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 600)->unique();
            $table->boolean('commentaries')->default(false);
            $table->string('instructions', 600)->nullable();
            $table->enum('type',['empty','radio','check','selector'])->default('empty'); /* multiple choice, text, number, date, etc */
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};

