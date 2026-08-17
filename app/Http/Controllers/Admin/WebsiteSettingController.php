<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::firstOrCreate(
            ['id' => 1],
            [
                'phone' => '+1 929 210 4402',
                'whatsapp' => '+1 929 210 4402',
                'email' => 'sales@prosix.com',

                'address_one' => '2604 Whittier Place Wilmington, Delaware 19808',
                'address_two' => '5556 E Kings Canyon Rd, Fresno, CA 93727',

                'opening_days' => 'Mon – Sat',
                'opening_status' => 'Open',
                'opening_time' => '08:00 – 18:00',

                'sunday_label' => 'Sunday',
                'sunday_status' => 'Closed',

                'subscribe_title' => 'SUBSCRIBE',
                'subscribe_subtitle' => 'To our newsletter for latest updates',

                'website_badge_text' => 'Need a Professional Website?',
                'website_badge_link' => '/website-request',

                'facebook_url' => 'https://www.facebook.com/prosixsports/',
                'instagram_url' => 'https://www.instagram.com/prosixsports',
                'youtube_url' => 'https://www.youtube.com/@prosixsports',
                'twitter_url' => 'https://x.com/ProsixSports',
                'pinterest_url' => 'https://www.pinterest.com/prosixsports/',

                'footer_texture_opacity' => 48,

                'show_facebook' => true,
                'show_instagram' => true,
                'show_youtube' => true,
                'show_twitter' => true,
                'show_pinterest' => true,

                'show_website_badge' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Old row support
        |--------------------------------------------------------------------------
        | Agar setting pehle create ho chuki thi aur opacity null hai,
        | to default 48 set kar do.
        */
        if ($setting->footer_texture_opacity === null) {
            $setting->footer_texture_opacity = 48;
            $setting->save();
        }

        return view(
            'admin.website-info.edit',
            compact('setting')
        );
    }


    public function update(Request $request)
    {
        $setting = WebsiteSetting::firstOrCreate(
            ['id' => 1]
        );


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([

            // CONTACT
            'phone' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',

            'address_one' => 'nullable|string|max:1000',
            'address_two' => 'nullable|string|max:1000',


            // OPENING SCHEDULE
            'opening_days' => 'nullable|string|max:255',
            'opening_status' => 'nullable|string|max:100',
            'opening_time' => 'nullable|string|max:255',

            'sunday_label' => 'nullable|string|max:100',
            'sunday_status' => 'nullable|string|max:100',


            // SUBSCRIBE
            'subscribe_title' => 'nullable|string|max:255',
            'subscribe_subtitle' => 'nullable|string|max:500',

            'website_badge_text' => 'nullable|string|max:255',
            'website_badge_link' => 'nullable|string|max:1000',


            // SOCIAL LINKS
            'facebook_url' => 'nullable|string|max:1000',
            'instagram_url' => 'nullable|string|max:1000',
            'youtube_url' => 'nullable|string|max:1000',
            'twitter_url' => 'nullable|string|max:1000',
            'pinterest_url' => 'nullable|string|max:1000',


            // FOOTER MEDIA
            'footer_logo_one' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'footer_logo_two' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',

            'footer_background' =>
                'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:10240',

            /*
            |--------------------------------------------------------------------------
            | FOOTER TEXTURE DARKNESS
            |--------------------------------------------------------------------------
            | 0   = texture full visible
            | 100 = texture almost hidden
            */
            'footer_texture_opacity' =>
                'nullable|integer|min:0|max:100',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Toggle values
        |--------------------------------------------------------------------------
        */
        $validated['show_facebook'] =
            $request->boolean('show_facebook');

        $validated['show_instagram'] =
            $request->boolean('show_instagram');

        $validated['show_youtube'] =
            $request->boolean('show_youtube');

        $validated['show_twitter'] =
            $request->boolean('show_twitter');

        $validated['show_pinterest'] =
            $request->boolean('show_pinterest');

        $validated['show_website_badge'] =
            $request->boolean('show_website_badge');


        /*
        |--------------------------------------------------------------------------
        | Footer Texture Opacity
        |--------------------------------------------------------------------------
        */
        $validated['footer_texture_opacity'] =
            (int) $request->input(
                'footer_texture_opacity',
                $setting->footer_texture_opacity ?? 48
            );


        /*
        |--------------------------------------------------------------------------
        | FOOTER LOGO ONE
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('footer_logo_one')) {

            if ($setting->footer_logo_one) {

                Storage::disk('public')->delete(
                    $setting->footer_logo_one
                );

            }


            $validated['footer_logo_one'] =
                $request
                    ->file('footer_logo_one')
                    ->store(
                        'website/footer',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER LOGO TWO
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('footer_logo_two')) {

            if ($setting->footer_logo_two) {

                Storage::disk('public')->delete(
                    $setting->footer_logo_two
                );

            }


            $validated['footer_logo_two'] =
                $request
                    ->file('footer_logo_two')
                    ->store(
                        'website/footer',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER BACKGROUND
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('footer_background')) {

            if ($setting->footer_background) {

                Storage::disk('public')->delete(
                    $setting->footer_background
                );

            }


            $validated['footer_background'] =
                $request
                    ->file('footer_background')
                    ->store(
                        'website/footer',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */
        $setting->update(
            $validated
        );


        return redirect()
            ->route(
                'admin.website-info.edit'
            )
            ->with(
                'success',
                'Website information updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PUBLIC WEBSITE INFO API
    |--------------------------------------------------------------------------
    */
    public function publicInfo()
    {
        $setting =
            WebsiteSetting::first();


        if (!$setting) {

            return response()->json([
                'data' => null
            ]);

        }


        return response()->json([

            'data' => [

                /*
                |--------------------------------------------------------------------------
                | CONTACT
                |--------------------------------------------------------------------------
                */
                'phone' =>
                    $setting->phone,

                'whatsapp' =>
                    $setting->whatsapp,

                'email' =>
                    $setting->email,


                'address_one' =>
                    $setting->address_one,

                'address_two' =>
                    $setting->address_two,


                /*
                |--------------------------------------------------------------------------
                | OPENING SCHEDULE
                |--------------------------------------------------------------------------
                */
                'opening_days' =>
                    $setting->opening_days,

                'opening_status' =>
                    $setting->opening_status,

                'opening_time' =>
                    $setting->opening_time,


                'sunday_label' =>
                    $setting->sunday_label,

                'sunday_status' =>
                    $setting->sunday_status,


                /*
                |--------------------------------------------------------------------------
                | SUBSCRIBE
                |--------------------------------------------------------------------------
                */
                'subscribe_title' =>
                    $setting->subscribe_title,

                'subscribe_subtitle' =>
                    $setting->subscribe_subtitle,


                'website_badge_text' =>
                    $setting->website_badge_text,

                'website_badge_link' =>
                    $setting->website_badge_link,


                /*
                |--------------------------------------------------------------------------
                | SOCIAL
                |--------------------------------------------------------------------------
                */
                'facebook_url' =>
                    $setting->facebook_url,

                'instagram_url' =>
                    $setting->instagram_url,

                'youtube_url' =>
                    $setting->youtube_url,

                'twitter_url' =>
                    $setting->twitter_url,

                'pinterest_url' =>
                    $setting->pinterest_url,


                /*
                |--------------------------------------------------------------------------
                | VISIBILITY
                |--------------------------------------------------------------------------
                */
                'show_facebook' =>
                    (bool) $setting->show_facebook,

                'show_instagram' =>
                    (bool) $setting->show_instagram,

                'show_youtube' =>
                    (bool) $setting->show_youtube,

                'show_twitter' =>
                    (bool) $setting->show_twitter,

                'show_pinterest' =>
                    (bool) $setting->show_pinterest,

                'show_website_badge' =>
                    (bool) $setting->show_website_badge,


                /*
                |--------------------------------------------------------------------------
                | FOOTER TEXTURE OPACITY
                |--------------------------------------------------------------------------
                */
                'footer_texture_opacity' =>
                    (int) (
                        $setting->footer_texture_opacity
                        ?? 48
                    ),


                /*
                |--------------------------------------------------------------------------
                | FOOTER LOGO ONE
                |--------------------------------------------------------------------------
                */
                'footer_logo_one' =>
                    $setting->footer_logo_one

                        ? asset(
                            'storage/' .
                            $setting->footer_logo_one
                        )

                        : asset(
                            'public/assets/images/P LOGO WHITE.png'
                        ),


                /*
                |--------------------------------------------------------------------------
                | FOOTER LOGO TWO
                |--------------------------------------------------------------------------
                */
                'footer_logo_two' =>
                    $setting->footer_logo_two

                        ? asset(
                            'storage/' .
                            $setting->footer_logo_two
                        )

                        : asset(
                            'public/assets/images/PROSIX SPORTS LOGO PNG WHITE.png'
                        ),


                /*
                |--------------------------------------------------------------------------
                | FOOTER BACKGROUND
                |--------------------------------------------------------------------------
                */
                'footer_background' =>
                    $setting->footer_background

                        ? asset(
                            'storage/' .
                            $setting->footer_background
                        )

                        : asset(
                            'public/assets/images/footer texture.svg'
                        ),

            ]

        ]);
    }
}
