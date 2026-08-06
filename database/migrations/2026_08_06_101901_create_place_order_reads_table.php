<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_order_reads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('place_order_id')
                ->constrained('place_orders')
                ->cascadeOnDelete();

            $table->string('source', 30);
            $table->string('viewer_id');
            $table->string('viewer_name')->nullable();
            $table->string('viewer_email')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique([
                'place_order_id',
                'source',
                'viewer_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_order_reads');
    }
};
