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
    Schema::table('categories', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        $table->boolean('highlight')->default(0)->after('status');
        $table->string('password')->nullable()->after('highlight');
        $table->unsignedBigInteger('navigation_id')->nullable()->after('password');
    });
}

public function down()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropColumn(['parent_id','highlight','password','navigation_id']);
    });
}

};
