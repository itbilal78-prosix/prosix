<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // اگر user logged in ہو تو
            $table->decimal('total', 10, 2); // ٹوٹل پرائس
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->string('payment_method'); // cod, easypaisa, jazzcash
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_province')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('delivery_days'); // 1-3, 3-5, 7+
            $table->json('items'); // cart items JSON میں store
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};