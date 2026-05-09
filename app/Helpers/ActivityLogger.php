<?php

namespace App\Helpers;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $targetName,
        int $targetId = null,
        array $changes = []
    ): void {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return;

        AdminActivityLog::create([
            'admin_id'    => $admin->id,
            'admin_name'  => $admin->name,
            'action'      => $action,
            'module'      => $module,
            'target_name' => $targetName,
            'target_id'   => $targetId,
            'changes'     => $changes ?: null,
            'ip_address'  => request()->ip(),
        ]);
    }
}
