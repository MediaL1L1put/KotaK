<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_id')->nullable()->index();
            $table->string('status')->default('pending');
            $table->boolean('paid')->default(false);
            $table->boolean('test')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('type'); // 'ticket' или 'booking'
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};