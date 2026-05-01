<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Pehle check karo column exist karta hai ya nahi
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->nullable()->after('id');
            }
        });

        // Purane orders ke liye order_number generate karo
        \App\Models\Order::whereNull('order_number')->each(function ($order) {
            $order->update([
                'order_number' => 'P6S-' . date('Y', strtotime($order->created_at)) . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT)
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
