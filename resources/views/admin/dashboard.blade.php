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
            <div class="dash-card-icon p-4" style="background:#111;">
                <i class="bi bi-currency-rupee text-white"></i>
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
            <div class="dash-card-icon p-4" style="background:#222;">
                <i class="bi bi-bag-check text-white"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Orders</span>
                <span class="dash-card-value">{{ $total_orders }}</span>
                ,
                <span class="dash-card-sub">{{ $pending_orders }} pending</span>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="dash-card-link"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    {{-- Users --}}
    <div class="col-6 col-xl-3">
        <div class="dash-card">
            <div class="dash-card-icon p-4" style="background:#333;">
                <i class="bi bi-people-fill text-white"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Users</span>
                <span class="dash-card-value">{{ $total_users }}</span>
                <span class="dash-card-sub">Registered</span>
            </div>
            <a href="{{ route('admin.users.index') }}" class="dash-card-link"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    {{-- Products --}}
    <div class="col-6 col-xl-3">
        <div class="dash-card">
            <div class="dash-card-icon p-4" style="background:#444;">
                <i class="bi bi-box-seam  text-white"></i>
            </div>
            <div class="dash-card-info">
                <span class="dash-card-label">Total Products</span>
                <span class="dash-card-value">{{ $total_products }}</span>
            </div>
            <a href="{{ route('products.index') }}" class="dash-card-link"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

</div>



{{-- ── RECENT ORDERS + QUICK ACTIONS ── --}}
<div class="row g-3">

    {{-- Recent Orders Table --}}
    <div class="col-12 col-xl-8">
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h4>Recent Orders</h4>
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
