<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       

        Schema::table('products', function (Blueprint $table) {
            // ── Shipping ──────────────────────────────────────────
            if (!Schema::hasColumn('products', 'shipping_enabled')) {
                $table->boolean('shipping_enabled')->default(true);
            }
            if (!Schema::hasColumn('products', 'shipping_cost')) {
                $table->decimal('shipping_cost', 8, 2)->nullable()->default(0);
            }
            if (!Schema::hasColumn('products', 'free_shipping_above')) {
                $table->decimal('free_shipping_above', 8, 2)->nullable();
            }

            // ── Stock ─────────────────────────────────────────────
            if (!Schema::hasColumn('products', 'in_stock')) {
                $table->boolean('in_stock')->default(true);
            }
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->unsignedInteger('stock_quantity')->nullable();
            }

            // ── Sizes (JSON array of enabled sizes) ───────────────
            if (!Schema::hasColumn('products', 'sizes')) {
                $table->json('sizes')->nullable();
            }

            // ── Colors (JSON: [{name, hex}]) ──────────────────────
            if (!Schema::hasColumn('products', 'colors')) {
                $table->json('colors')->nullable();
            }

            // ── Gallery Images (JSON array of paths) ──────────────
            if (!Schema::hasColumn('products', 'gallery_images')) {
                $table->json('gallery_images')->nullable();
            }

            // ── Size Chart Image ──────────────────────────────────
            if (!Schema::hasColumn('products', 'size_chart_image')) {
                $table->string('size_chart_image')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_enabled', 'shipping_cost', 'free_shipping_above',
                'in_stock', 'stock_quantity',
                'sizes', 'colors', 'gallery_images', 'size_chart_image',
            ]);
        });
    }
};
