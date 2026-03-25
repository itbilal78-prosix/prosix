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
    Schema::table('orders', function (Blueprint $table) {

        $table->string('order_number')->unique()->after('id');

        $table->string('payment_status')->default('pending')->after('status');

        $table->string('currency')->default('usd')->after('payment_method');

        $table->string('stripe_payment_intent_id')->nullable();

        $table->string('stripe_charge_id')->nullable();

        $table->string('stripe_session_id')->nullable();

        $table->string('tracking_number')->nullable();

        $table->string('courier_name')->nullable();

        $table->date('dispatch_date')->nullable();

        $table->date('delivered_date')->nullable();

        $table->text('admin_notes')->nullable();

        $table->decimal('paid_amount', 10, 2)->nullable();

        $table->timestamp('transaction_date')->nullable();

    });
}

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {

        $table->dropColumn([
            'order_number',
            'payment_status',
            'currency',
            'stripe_payment_intent_id',
            'stripe_charge_id',
            'stripe_session_id',
            'tracking_number',
            'courier_name',
            'dispatch_date',
            'delivered_date',
            'admin_notes',
            'paid_amount',
            'transaction_date'
        ]);

    });
}
};
