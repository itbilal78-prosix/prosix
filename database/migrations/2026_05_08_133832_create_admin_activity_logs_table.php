<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('admin_name');        // delete hone ke baad bhi naam dikhe
            $table->string('action');            // created, updated, deleted
            $table->string('module');            // Admin, Order, Product, Category
            $table->string('target_name')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('changes')->nullable(); // old vs new values
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
