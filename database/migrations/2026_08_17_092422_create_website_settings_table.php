<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();

            // Company / Contact
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            $table->text('address_one')->nullable();
            $table->text('address_two')->nullable();

            // Opening Schedule
            $table->string('opening_days')->nullable();
            $table->string('opening_status')->default('Open');
            $table->string('opening_time')->nullable();

            $table->string('sunday_label')->default('Sunday');
            $table->string('sunday_status')->default('Closed');

            // Subscribe
            $table->string('subscribe_title')->default('SUBSCRIBE');
            $table->string('subscribe_subtitle')->nullable();

            // Website badge
            $table->string('website_badge_text')->nullable();
            $table->string('website_badge_link')->nullable();

            // Social links
            $table->text('facebook_url')->nullable();
            $table->text('instagram_url')->nullable();
            $table->text('youtube_url')->nullable();
            $table->text('twitter_url')->nullable();
            $table->text('pinterest_url')->nullable();

            // Footer assets
            $table->string('footer_logo_one')->nullable();
            $table->string('footer_logo_two')->nullable();
            $table->string('footer_background')->nullable();

            $table->boolean('show_facebook')->default(true);
            $table->boolean('show_instagram')->default(true);
            $table->boolean('show_youtube')->default(true);
            $table->boolean('show_twitter')->default(true);
            $table->boolean('show_pinterest')->default(true);

            $table->boolean('show_website_badge')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
