<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('instagram')->nullable();
            $table->string('address')->nullable();
            $table->string('team_name')->nullable();
            $table->string('role')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('team_color')->nullable();
            $table->string('home_away')->nullable();
            $table->string('design_style')->nullable();
            $table->string('material')->nullable();
            $table->json('products')->nullable();
            $table->text('additional')->nullable();
            $table->string('source')->nullable();

            // Image / Artwork file field
            $table->string('artwork_file')->nullable()->comment('Stored filename of uploaded artwork/design file');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_requests');
    }
};