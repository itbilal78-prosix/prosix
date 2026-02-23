<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->longText('mascot_changes')->nullable()->after('pattern_changes');
        });
    }

    public function down(): void
    {
        Schema::table('customizer_models', function (Blueprint $table) {
            $table->dropColumn('mascot_changes');
        });
    }
};
