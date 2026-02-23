<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('social_medias', function (Blueprint $table) {
        $table->id();
        $table->string('name');       // Facebook, Twitter, etc.
        $table->string('logo');       // Icon class or image path
        $table->string('link');       // URL
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_medias');
    }
};
