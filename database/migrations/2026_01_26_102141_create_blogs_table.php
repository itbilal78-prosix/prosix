<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('body');
            $table->string('image')->nullable();
            $table->string('video')->nullable(); // YouTube or hosted link
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('blogs');
    }
};
