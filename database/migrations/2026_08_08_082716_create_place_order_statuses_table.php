<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_order_statuses', function (Blueprint $table) {
            $table->id();

            $table->string('value', 100)->unique();
            $table->string('name', 100);
            $table->string('color', 7)->default('#667085');

            $table->boolean('is_custom')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });

        DB::table('place_order_statuses')->insert([
            [
                'value' => 'pending',
                'name' => 'Pending',
                'color' => '#f59e0b',
                'is_custom' => false,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 'processing',
                'name' => 'Processing',
                'color' => '#0ea5e9',
                'is_custom' => false,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 'completed',
                'name' => 'Completed',
                'color' => '#10b981',
                'is_custom' => false,
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value' => 'cancelled',
                'name' => 'Cancelled',
                'color' => '#ef4444',
                'is_custom' => false,
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('place_order_statuses');
    }
};
