<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->unsignedBigInteger('institution_id'); 
            $table->foreign('institution_id')->references('id')->on('institutions')->cascadeOnUpdate()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('institution_id'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};



