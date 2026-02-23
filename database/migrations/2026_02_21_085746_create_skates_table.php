<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('skates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skate_model_id')->constrained()->onDelete('cascade');
            $table->integer('size');
            $table->integer('quantity');
            $table->integer('available_quantity');
            $table->decimal('price_per_hour', 8, 2)->default(150);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('skates');
    }
};