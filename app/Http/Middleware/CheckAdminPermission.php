<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        $admin = auth('admin')->user();

        // ✅ Super admin always allowed
        if ($admin->is_super_admin) {
            return $next($request);
        }

        if (! $admin->$permission) {
            abort(403, 'Access Denied');
        }

        return $next($request);
    }
}
