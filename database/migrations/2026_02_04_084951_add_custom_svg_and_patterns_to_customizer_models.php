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
    Schema::table('customizer_models', function (Blueprint $table) {

        $table->json('pattern_changes')->nullable()->after('color_changes');

        // OLD
        $table->string('custom_svg')->nullable()->after('pattern_changes');

        // NEW MULTI VIEW
        $table->string('custom_front_svg')->nullable()->after('custom_svg');
        $table->string('custom_back_svg')->nullable();
        $table->string('custom_left_svg')->nullable();
        $table->string('custom_right_svg')->nullable();

    });
}


public function down(): void
{
    Schema::table('customizer_models', function (Blueprint $table) {

        $table->dropColumn([
            'pattern_changes',
            'custom_svg',
            'custom_front_svg',
            'custom_back_svg',
            'custom_left_svg',
            'custom_right_svg'
        ]);

    });
}


};
