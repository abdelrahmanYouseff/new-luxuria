<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupon_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->integer('coupon_id');
            $table->string('coupon_name');
            $table->string('coupon_code')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_session_id')->nullable();
            $table->string('payment_status')->default('pending'); // pending, completed, failed, refunded
            $table->string('payment_method')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->json('payment_details')->nullable(); // Store additional payment info
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'payment_status']);
            $table->index(['stripe_payment_intent_id']);
            $table->index(['invoice_number']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_invoices');
    }
};
