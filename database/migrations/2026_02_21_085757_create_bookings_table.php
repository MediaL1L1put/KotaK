<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone');
            $table->integer('hours');
            $table->foreignId('skate_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('skate_size')->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_id')->nullable();
            $table->string('payment_status')->default('pending');
            $table->boolean('has_own_skates')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};