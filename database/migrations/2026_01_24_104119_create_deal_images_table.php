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
    Schema::create('deal_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('deal_id')->constrained()->onDelete('cascade');
        $table->string('image_path'); // /storage/deals/abc.jpg
        $table->string('link')->nullable(); // har image ka alag link
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_images');
    }
};
