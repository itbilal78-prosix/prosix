<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ Custom email verification URL
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return config('app.frontend_url') // frontend URL
                . '/verify-email?id=' . $notifiable->getKey()
                . '&hash=' . sha1($notifiable->getEmailForVerification());
        });
    }
}
