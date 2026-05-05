<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        $host = $request->getHost();

        if (str_contains($host, 'admin.') || str_contains($host, 'customizer.')) {
            return route('admin.login');
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        }

        return null;
    }
}
