<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flipbooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path'); // uploaded file
            $table->timestamps();
        });
    }
 
public function down(): void
{
    Schema::dropIfExists('flipbooks');
}
};
