<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_images', function (Blueprint $table) {
            $table->softDeletes(); // ✅ yeh add karo
        });
    }

    public function down(): void
    {
        Schema::table('deal_images', function (Blueprint $table) {
            $table->dropSoftDeletes(); // ✅ yeh add karo
        });
    }
};
