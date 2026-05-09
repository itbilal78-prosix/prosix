<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id', 'admin_name', 'action',
        'module', 'target_name', 'target_id',
        'changes', 'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Action badge color helper
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created'        => 'success',
            'updated'        => 'warning',
            'deleted'        => 'danger',
            'status_changed' => 'info',
            default          => 'secondary',
        };
    }
}
