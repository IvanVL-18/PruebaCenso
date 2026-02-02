<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('index_for_censos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('censo_id')->constrained('censos')->cascadeOnDelete();
            $table->foreignId('index_id')->constrained('indexs')->cascadeOnDelete();
            // Relación polimórfica (hacia sections o units)
            $table->string('reference_type', 45);
            $table->unsignedBigInteger('reference_id');
            $table->tinyInteger('change')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('index_for_censos');
    }
};
