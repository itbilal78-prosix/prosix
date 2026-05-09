<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::query()->latest();

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs   = $query->paginate(30)->withQueryString();
        $admins = Admin::where('is_super_admin', false)->orderBy('name')->get();

        return view('admin.activity-logs.index', compact('logs', 'admins'));
    }
}
