@extends('layouts.dashboard')

@section('title', 'Dashboard')

@push('styles')
<style>
/* ── STAT CARDS ── */
.dash-card {
    background: #fff;
    border: 1px solid #efefef;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: box-shadow 0.2s, transform 0.2s;
}
.dash-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.09);
    transform: translateY(-2px);
}
.dash-card-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #fff;
    font-size: 22px;
}
.dash-card-info { flex: 1; display: flex; flex-direction: column; }
.dash-card-label { font-size: 11.5px; color: #999; font-weight: 500; text-transform: uppercase; letter-spacing: .5px; }
.dash-card-value { font-size: 28px; font-weight: 700; color: #111; line-height: 1.2; }
.dash-card-sub   { font-size: 11px; color: #bbb; margin-top: 2px; }
.dash-card-link  { color: #ccc; font-size: 18px; text-decoration: none; transition: color .2s; }
.dash-card-link:hover { color: #000; }

/* ── PANELS ── */
.dash-panel {
    background: #fff;
    border: 1px solid #efefef;
    border-radius: 14px;
    overflow: hidden;
}
.dash-panel-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f3f3;
}
.dash-panel-header h6 { font-size: 14px; font-weight: 600; color: #111; }

/* ── TABLE ── */
.dash-table thead th {
    background: #f9f9f9;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .6px;
    color: #777; padding: 11px 16px; border: none;
}
.dash-table tbody td {
    padding: 11px 16px; font-size: 13px;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: middle; color: #333;
}
.dash-table tbody tr:last-child td { border-bottom: none; }
.dash-table tbody tr:hover { background: #fafafa; }

/* ── QUICK LINKS ── */
.quick-link {
    display: flex; align-items: center;
    padding: 10px 14px; border-radius: 8px;
    color: #333; text-decoration: none;
    font-size: 13px; font-weight: 500;
    transition: all .2s;
    border: 1px solid #eee;
}
.quick-link:hover { background: #000; color: #fff; border-color: #000; }
.quick-link i { font-size: 15px; }

/* ── CHART WRAPPER ── */
.chart-wrap { padding: 16px 20px; }
canvas { max-height: 220px; }
</style>
@endpush

@section('content')

{{-- TOP BAR --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#111;">Dashboard</h4>
        <small class="text-muted">Welcome back, <strong>{{ auth()->guard('admin')->user()->name }}</strong></small>
    </div>
    <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}</span>
</div>

{{-- ── STAT CARDS ── --}}
<div class="row g-3 mb-4">

    {{-- Revenue --}}
    <div class="col-6 col-xl-3">
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#111;">
                <i class="bi bi-currency-rupee"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Revenue</span>
                <span class="dash-card-value">{{ number_format($total_revenue, 0) }}</span>
                <span class="dash-card-sub">Excluding cancelled</span>
            </div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="col-6 col-xl-3">
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#222;">
                <i class="bi bi-bag-check"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Orders</span>
                <span class="dash-card-value">{{ $total_orders }}</span>
                <span class="dash-card-sub">{{ $pending_orders }} pending</span>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="dash-card-link"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    {{-- Users --}}
    <div class="col-6 col-xl-3">
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#333;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Users</span>
                <span class="dash-card-value">{{ $total_users }}</span>
                <span class="dash-card-sub">Registered customers</span>
            </div>
            <a href="{{ route('admin.users.index') }}" class="dash-card-link"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    {{-- Products --}}
    <div class="col-6 col-xl-3">
        <div class="dash-card">
            <div class="dash-card-icon" style="background:#444;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Products</span>
                <span class="dash-card-value">{{ $total_products }}</span>
                <span class="dash-card-sub">In catalogue</span>
            </div>
            <a href="{{ route('products.index') }}" class="dash-card-link"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

</div>

{{-- ── CHARTS ROW ── --}}
<div class="row g-3 mb-4">

    {{-- Orders Line Chart --}}
    <div class="col-12 col-xl-5">
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h6>Orders – Last 7 Days</h6>
            </div>
            <div class="chart-wrap">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Revenue Bar Chart --}}
    <div class="col-12 col-xl-4">
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h6>Revenue – Last 7 Days</h6>
            </div>
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Order Status Pie --}}
    <div class="col-12 col-xl-3">
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h6>Order Status</h6>
            </div>
            <div class="chart-wrap d-flex justify-content-center">
                <canvas id="statusChart" style="max-height:200px;max-width:200px;"></canvas>
            </div>
            {{-- Legend --}}
            <div class="px-3 pb-3">
                @foreach($status_data as $s)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-capitalize">{{ $s->status }}</span>
                    <span class="badge bg-dark">{{ $s->count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ── RECENT ORDERS + QUICK ACTIONS ── --}}
<div class="row g-3">

    {{-- Recent Orders Table --}}
    <div class="col-12 col-xl-8">
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h6>Recent Orders</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-dark px-3">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table dash-table mb-0">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders as $order)
                        <tr>
                            <td class="fw-semibold">#{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? $order->shipping_name ?? 'Guest' }}</td>
                            <td class="fw-semibold">Rs. {{ number_format($order->total, 0) }}</td>
                            <td class="text-capitalize text-muted small">{{ $order->payment_method ?? '-' }}</td>
                            <td>
                                @php
                                    $badge = match($order->status) {
                                        'delivered' => 'success',
                                        'shipped'   => 'primary',
                                        'pending'   => 'warning',
                                        'cancelled' => 'danger',
                                        'processing'=> 'info',
                                        default     => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No orders yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-12 col-xl-4">
        <div class="dash-panel">
            <div class="dash-panel-header"><h6>Quick Actions</h6></div>
            <div class="d-flex flex-column gap-2 p-3">
                <a href="{{ route('products.create') }}"         class="quick-link"><i class="bi bi-plus-circle me-2"></i> Add New Product</a>
                <a href="{{ route('categories.index') }}"        class="quick-link"><i class="bi bi-tags me-2"></i> Manage Categories</a>
                <a href="{{ route('banners.index') }}"           class="quick-link"><i class="bi bi-image me-2"></i> Manage Banners</a>
                <a href="{{ route('deals.index') }}"             class="quick-link"><i class="bi bi-gift me-2"></i> Deals & Offers</a>
                <a href="{{ route('blogs.index') }}"             class="quick-link"><i class="bi bi-newspaper me-2"></i> Blog / News</a>
                <a href="{{ route('admin.flipbooks.index') }}"   class="quick-link"><i class="bi bi-book-half me-2"></i> Flip Books</a>
                <a href="{{ route('admin.users.index') }}"       class="quick-link"><i class="bi bi-people me-2"></i> All Users</a>
                <a href="{{ route('admin.admins.index') }}"      class="quick-link"><i class="bi bi-person-gear me-2"></i> Manage Admins</a>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels  = @json($chart_labels);
    const orders  = @json($chart_orders);
    const revenue = @json($chart_revenue);
    const users   = @json($chart_users);

    // ── Orders Line Chart ──
    new Chart(document.getElementById('ordersChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Orders',
                data: orders,
                borderColor: '#000',
                backgroundColor: 'rgba(0,0,0,0.06)',
                borderWidth: 2,
                pointBackgroundColor: '#000',
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ── Revenue Bar Chart ──
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Revenue (Rs)',
                data: revenue,
                backgroundColor: 'rgba(0,0,0,0.80)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ── Status Pie Chart ──
    const statusLabels = @json($status_data->pluck('status'));
    const statusCounts = @json($status_data->pluck('count'));
    const pieColors    = ['#111','#444','#777','#aaa','#ddd','#000'];

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: pieColors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            cutout: '65%'
        }
    });
</script>
@endpush
