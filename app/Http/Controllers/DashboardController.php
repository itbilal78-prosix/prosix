<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index()
{
    $admin = auth()->guard('admin')->user();

    // ── SUB ADMIN — alag view ──
    if (!$admin->is_super_admin) {
        return view('admin.dashboard-sub', compact('admin'));
    }

    // ── STAT CARDS ──
    $total_products = Product::count();
    $total_orders   = Order::count();
    $total_users    = User::count();
    $pending_orders = Order::where('status', 'pending')->count();
    $total_revenue  = Order::where('status', '!=', 'cancelled')->sum('total');

    $recent_orders = Order::with('user')->latest()->take(8)->get();

    $orders_chart = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as revenue')
        )
        ->where('created_at', '>=', now()->subDays(6)->startOfDay())
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $chart_labels = $chart_orders = $chart_revenue = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $row  = $orders_chart->firstWhere('date', $date);
        $chart_labels[]  = now()->subDays($i)->format('d M');
        $chart_orders[]  = $row ? (int) $row->count   : 0;
        $chart_revenue[] = $row ? (float) $row->revenue : 0;
    }

    $users_chart = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(6)->startOfDay())
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $chart_users = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $row  = $users_chart->firstWhere('date', $date);
        $chart_users[] = $row ? (int) $row->count : 0;
    }

    $status_data = Order::select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    return view('admin.dashboard', compact(
        'total_products', 'total_orders', 'total_users',
        'pending_orders', 'total_revenue', 'recent_orders',
        'chart_labels', 'chart_orders', 'chart_revenue',
        'chart_users', 'status_data'
    ));
}


    public function dashboardStats()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'error' => 'User not authenticated'
        ], 401);
    }

    return response()->json([

        'total_orders' =>
            \App\Models\Order::where('user_id', $user->id)->count(),

        'pending_orders' =>
            \App\Models\Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),

        'delivered_orders' =>
            \App\Models\Order::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->count(),

        'place_orders' =>
            \App\Models\PlaceOrder::where('user_id', $user->id)->count(),

        'my_requests' =>
            \App\Models\ArtworkRequest::where('user_id', $user->id)->count(),

        'total_spent' =>
            \App\Models\Order::where('user_id', $user->id)->sum('total')

    ]);
}
}
