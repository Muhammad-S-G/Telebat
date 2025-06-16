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
            $table->morphs('model');

            $table->foreignId('payment_request_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->string('payment_method', 50);
            $table->string('status', 50);

            $table->string('paypal_order_id')->nullable();
            $table->json('paypal_capture_response')->nullable();

            $table->string('stripe_client_secret')->nullable();
            $table->json('stripe_webhook_payload')->nullable();

            $table->json('response')->nullable();
            $table->text('description')->nullable(); 

            $table->unsignedBigInteger('price');
            $table->string('currency', 3)->default('usd');

            $table->timestamp('completed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['payment_method', 'status']);
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
