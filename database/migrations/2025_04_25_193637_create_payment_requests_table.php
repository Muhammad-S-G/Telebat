<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->json('title');
            $table->json('description');

            $table->unsignedBigInteger('price');

            $table->string('currency', 3)
                ->default('usd');

            $table->string('stripe_payment_intent_id')
                ->nullable();

            $table->string('status', 20)->default('PENDING');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
