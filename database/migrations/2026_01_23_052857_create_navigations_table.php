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
       Schema::create('navigations', function (Blueprint $table) {
    $table->id();
    $table->string('title');          // Home, Team Store, Support
    $table->string('slug')->unique(); // home, team-store
    $table->string('route')->nullable(); // /, /contact
    $table->boolean('has_dropdown')->default(false);
    $table->integer('position')->default(0);
    $table->boolean('status')->default(true);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigations');
    }
};
