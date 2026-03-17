<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('place_orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('full_name');        // ← YE ADD KARO
        $table->string('email');            // ← YE ADD KARO
        $table->string('order_number')->unique();
        $table->string('order_date');
        $table->string('delivery_date')->nullable();
        $table->string('sales_rep')->nullable();
        $table->string('team_colors')->nullable();
        $table->text('notes')->nullable();
        $table->json('mockup_files')->nullable();
        $table->json('roster_files')->nullable();
        $table->json('quote_files')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('place_orders');
    }
};
