<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_customizations', function (Blueprint $table) {
            // Naye columns add karo jo existing table mein nahi hain
            if (!Schema::hasColumn('user_customizations', 'gradient_changes')) {
                $table->json('gradient_changes')->nullable()->after('color_changes');
            }
            if (!Schema::hasColumn('user_customizations', 'applications')) {
                $table->json('applications')->nullable()->after('pattern_changes');
            }
            if (!Schema::hasColumn('user_customizations', 'svg_content')) {
                $table->longText('svg_content')->nullable()->after('applications');
            }
            if (!Schema::hasColumn('user_customizations', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('svg_content');
            }
            if (!Schema::hasColumn('user_customizations', 'current_view')) {
                $table->string('current_view')->default('front')->after('thumbnail');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_customizations', function (Blueprint $table) {
            $table->dropColumn([
                'gradient_changes',
                'applications',
                'svg_content',
                'thumbnail',
                'current_view',
            ]);
        });
    }
};
