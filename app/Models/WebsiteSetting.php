<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [

        // =========================
        // CONTACT
        // =========================
        'phone',
        'whatsapp',
        'email',

        'address_one',
        'address_two',


        // =========================
        // OPENING SCHEDULE
        // =========================
        'opening_days',
        'opening_status',
        'opening_time',

        'sunday_label',
        'sunday_status',


        // =========================
        // SUBSCRIBE SECTION
        // =========================
        'subscribe_title',
        'subscribe_subtitle',

        'website_badge_text',
        'website_badge_link',


        // =========================
        // SOCIAL LINKS
        // =========================
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'twitter_url',
        'pinterest_url',


        // =========================
        // FOOTER MEDIA
        // =========================
        'footer_logo_one',
        'footer_logo_two',
        'footer_background',

        // Footer texture darkness / opacity
        'footer_texture_opacity',


        // =========================
        // SOCIAL VISIBILITY
        // =========================
        'show_facebook',
        'show_instagram',
        'show_youtube',
        'show_twitter',
        'show_pinterest',


        // =========================
        // WEBSITE BADGE VISIBILITY
        // =========================
        'show_website_badge',
    ];


    protected $casts = [

        // Social visibility
        'show_facebook' => 'boolean',
        'show_instagram' => 'boolean',
        'show_youtube' => 'boolean',
        'show_twitter' => 'boolean',
        'show_pinterest' => 'boolean',

        // Website badge
        'show_website_badge' => 'boolean',

        // Texture opacity: 0 - 100
        'footer_texture_opacity' => 'integer',
    ];
}
