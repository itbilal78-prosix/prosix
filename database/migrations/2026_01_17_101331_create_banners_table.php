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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Banner Title
            $table->string('title');

            // Button
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();

            // Images
            $table->string('background_image'); // background image
            $table->string('png_image')->nullable(); // front PNG image

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
