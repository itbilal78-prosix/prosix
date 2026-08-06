<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teamstore_order_reads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->string('viewer_id');
            $table->string('viewer_name')->nullable();
            $table->string('viewer_email')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique([
                'order_id',
                'viewer_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teamstore_order_reads');
    }
};
