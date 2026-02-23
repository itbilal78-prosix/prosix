<?php

// database/migrations/xxxx_create_membership_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('membership_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('organization');
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone');
            $table->string('role');
            $table->json('sports'); // store as JSON array
            $table->string('level');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('membership_requests');
    }
};
