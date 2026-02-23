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
        Schema::create('customizer_models', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Price in dollars
            $table->decimal('price', 10, 2)->nullable()->comment('Price in USD');

            // Front view
            $table->string('front_black')->nullable();
            $table->string('front_white')->nullable();
            $table->string('front_svg')->nullable();

            // Back view
            $table->string('back_black')->nullable();
            $table->string('back_white')->nullable();
            $table->string('back_svg')->nullable();

            // Left view
            $table->string('left_black')->nullable();
            $table->string('left_white')->nullable();
            $table->string('left_svg')->nullable();

            // Right view
            $table->string('right_black')->nullable();
            $table->string('right_white')->nullable();
            $table->string('right_svg')->nullable();

            // Thumbnail
            $table->string('thumbnail')->nullable();

            // Optional assignment
            $table->unsignedBigInteger('navigation_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customizer_models');
    }
};
