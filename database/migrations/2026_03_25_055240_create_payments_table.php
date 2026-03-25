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
    Schema::create('payments', function (Blueprint $table) {

        $table->id();

        $table->foreignId('order_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->string('payment_method');

        $table->decimal('amount', 10, 2);

        $table->string('currency')->default('usd');

        $table->string('payment_status')->default('pending');

        $table->string('stripe_payment_intent_id')->nullable();

        $table->string('stripe_charge_id')->nullable();

        $table->string('stripe_session_id')->nullable();

        $table->timestamp('transaction_date')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
  public function down(): void
{
    Schema::dropIfExists('payments');
}

};
