<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_requests', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('artwork_file');
        });

        Schema::table('membership_requests', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('artwork_requests', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });

        Schema::table('membership_requests', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
