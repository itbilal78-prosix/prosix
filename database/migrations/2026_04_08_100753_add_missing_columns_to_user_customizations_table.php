<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_customizations', function (Blueprint $table) {
            // Sirf woh columns add karo jo exist nahi karte
            if (!Schema::hasColumn('user_customizations', 'mascot_changes')) {
                $table->json('mascot_changes')->nullable();
            }
            if (!Schema::hasColumn('user_customizations', 'applications')) {
                $table->json('applications')->nullable();
            }
            if (!Schema::hasColumn('user_customizations', 'thumbnail')) {
                $table->string('thumbnail')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_customizations', function (Blueprint $table) {
            $table->dropColumn(['mascot_changes', 'applications', 'thumbnail']);
        });
    }
};
