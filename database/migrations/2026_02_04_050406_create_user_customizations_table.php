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
    Schema::create('user_customizations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('customizer_model_id')->constrained('customizer_models')->onDelete('cascade');
        $table->string('name')->nullable();                     // "My Red Home Kit", "Team Away 2025" etc
        $table->json('color_changes')->nullable();
        $table->json('pattern_changes')->nullable();            // agar pattern bhi save kar rahe ho
        $table->text('notes')->nullable();
        $table->boolean('is_public')->default(false);           // future sharing ke liye
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_customizations');
    }
};
