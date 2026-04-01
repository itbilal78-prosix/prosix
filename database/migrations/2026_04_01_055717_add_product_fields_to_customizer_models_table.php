<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            // Stock
            $table->boolean('in_stock')->default(true)->after('price');
            $table->unsignedInteger('stock_quantity')->nullable()->after('in_stock');

            // Shipping
            $table->boolean('shipping_enabled')->default(true)->after('stock_quantity');
            $table->decimal('shipping_cost', 8, 2)->default(0)->after('shipping_enabled');
            $table->decimal('free_shipping_above', 8, 2)->nullable()->after('shipping_cost');

            // Sizes (JSON array e.g. ["S","M","L","XL"])
            $table->json('sizes')->nullable()->after('free_shipping_above');

            // Size chart image path
            $table->string('size_chart_image')->nullable()->after('sizes');

            // Colors JSON — each color: { name, hex, views: { front:{black,white,svg}, back:..., left:..., right:... } }
            $table->json('colors_data')->nullable()->after('size_chart_image');
        });
    }

    public function down(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->dropColumn([
                'in_stock',
                'stock_quantity',
                'shipping_enabled',
                'shipping_cost',
                'free_shipping_above',
                'sizes',
                'size_chart_image',
                'colors_data',
            ]);
        });
    }
};
