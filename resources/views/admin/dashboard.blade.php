@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap');

.db * { box-sizing: border-box; }
.db { font-family: 'DM Sans', sans-serif; color: #111; }

/* ── TOP BAR ── */
.db-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
}
.db-top h2 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #0a0a0a;
    margin: 0 0 4px;
    letter-spacing: -0.5px;
}
.db-top .welcome {
    font-size: 15px;
    color: #888;
    font-weight: 400;
}
.db-top .welcome strong { color: #333; font-weight: 600; }
.db-date {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 50px;
    padding: 10px 18px;
    font-size: 14px;
    color: #555;
    font-weight: 500;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

/* ── STAT CARDS ── */
.db-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}
@media(max-width:1100px){ .db-cards { grid-template-columns: repeat(2,1fr); } }

.db-card {
    background: #fff;
    border-radius: 20px;
    padding: 14px 14px 14px;
    border: 1px solid #efefef;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}
.db-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.09); }
.db-card.dark { background: #0f0f0f; border-color: #0f0f0f; box-shadow: 0 4px 20px rgba(0,0,0,0.25); }

.db-card-ico {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-bottom: 18px;
}
.db-card-lbl {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #aaa;
    margin-bottom: 8px;
}
.db-card.dark .db-card-lbl { color: #555; }

.db-card-val {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 38px;
    font-weight: 700;
    color: #0a0a0a;
    line-height: 1;
    margin-bottom: 6px;
    letter-spacing: -1px;
}
.db-card.dark .db-card-val { color: #fff; }

.db-card-sub {
    font-size: 13px;
    color: #bbb;
    font-weight: 400;
}
.db-card-link {
    position: absolute;
    bottom: 20px; right: 20px;
    width: 30px; height: 30px;
    border-radius: 50%;
    background: #f2f2f2;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    color: #333;
    text-decoration: none;
    transition: all .15s;
}
.db-card.dark .db-card-link { background: #222; color: #fff; }
.db-card-link:hover { background: #222; color: #fff !important; }

/* ── LAYOUT ── */
.db-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 18px;
    align-items: start;
}
@media(max-width:1100px){ .db-grid { grid-template-columns: 1fr; } }

/* ── PANEL ── */
.db-panel {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #efefef;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    margin-bottom: 18px;
}
.db-panel:last-child { margin-bottom: 0; }

.db-ph {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid #f5f5f5;
}
.db-ph h5 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #0a0a0a;
    margin: 0;
    letter-spacing: -0.3px;
}
.db-ph-sub { font-size: 12px; color: #bbb; font-weight: 500; }
.db-view-all {
    font-size: 13px;
    font-weight: 600;
    color: #111;
    text-decoration: none;
    background: #f5f5f5;
    padding: 6px 16px;
    border-radius: 50px;
    border: 1px solid #e5e5e5;
    transition: all .15s;
}
.db-view-all:hover { background: #111; color: #fff !important; }

/* ── TABLE ── */
.db-tbl { width: 100%; border-collapse: collapse; }
.db-tbl thead th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #bbb;
    padding: 12px 18px;
    border-bottom: 1px solid #f5f5f5;
    text-align: left;
    white-space: nowrap;
}
.db-tbl tbody td {
    padding: 14px 18px;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #fafafa;
    vertical-align: middle;
}
.db-tbl tbody tr:last-child td { border-bottom: none; }
.db-tbl tbody tr:hover td { background: #fafafa; }

/* STATUS PILLS */
.sp {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .2px;
}
.sp-delivered  { background:#e8f5e9; color:#2e7d32; }
.sp-shipped    { background:#e3f2fd; color:#1565c0; }
.sp-pending    { background:#fff8e1; color:#e65100; }
.sp-cancelled  { background:#fce4ec; color:#c62828; }
.sp-processing { background:#f3e5f5; color:#6a1b9a; }
.sp-confirmed  { background:#e0f2f1; color:#00695c; }
.sp-default    { background:#f5f5f5; color:#666; }

/* ── DONUT ── */
.db-donut-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 20px 20px 20px;
}
.db-donut-mid {
    position: absolute;
    text-align: center;
    pointer-events: none;
}
.db-donut-num {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 26px;
    font-weight: 700;
    color: #111;
    line-height: 1;
}
.db-donut-tag {
    font-size: 11px;
    color: #aaa;
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-top: 2px;
}

/* ── LEGEND ── */
.db-legend {
    padding: 4px 0 8px;
}
.db-leg-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 22px;
    border-bottom: 1px solid #fafafa;
    font-size: 13px;
}
.db-leg-row:last-child { border-bottom: none; }
.db-leg-left { display: flex; align-items: center; gap: 9px; color: #444; font-weight: 500; }
.db-leg-dot  { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.db-leg-num  { font-weight: 700; color: #111; font-size: 14px; }

/* ── QUICK ACTIONS ── */
.db-qa-list { padding: 12px 16px; }
.db-qa {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 13px 14px;
    border-radius: 12px;
    border: 1px solid #efefef;
    background: #fafafa;
    text-decoration: none;
    color: #222;
    font-size: 14px;
    font-weight: 500;
    transition: all .15s;
    margin-bottom: 8px;
}
.db-qa:last-child { margin-bottom: 0; }
.db-qa:hover { background: #111; color: #fff !important; border-color: #111; }
.db-qa:hover .db-qa-ico { background: #333; color: #fff; }
.db-qa-ico {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: #ebebeb;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    transition: all .15s;
    flex-shrink: 0;
}
</style>

<div class="db">

    {{-- TOP --}}
    <div class="db-top">
        <div>
            <h2>Dashboard</h2>
            <div class="welcome">Welcome back, <strong>{{ auth()->guard('admin')->user()->name }}</strong></div>
        </div>
        <div class="db-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->format('d M Y') }}
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="db-cards">

        {{-- Revenue — Dark --}}
        <div class="db-card dark">
            <div class="db-card-ico" style="background:#1c1c1c;">
                <i class="bi bi-currency-dollar" style="color:#fff; font-size:22px;"></i>
            </div>
            <div class="db-card-lbl">Total Revenue</div>
            <div class="db-card-val">${{ number_format($total_revenue, 0) }}</div>
            <div class="db-card-sub">Excluding cancelled</div>
        </div>

        {{-- Orders --}}
        <div class="db-card">
            <div class="db-card-ico" style="background:#f2f2f2;">
                <i class="bi bi-bag-check" style="color:#111; font-size:20px;"></i>
            </div>
            <div class="db-card-lbl">Total Orders</div>
            <div class="db-card-val">{{ $total_orders }}</div>
            <div class="db-card-sub">{{ $pending_orders }} pending</div>
            <a href="{{ route('admin.orders.index') }}" class="db-card-link">
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        {{-- Users --}}
        <div class="db-card">
            <div class="db-card-ico" style="background:#f2f2f2;">
                <i class="bi bi-people-fill" style="color:#111; font-size:20px;"></i>
            </div>
            <div class="db-card-lbl">Total Users</div>
            <div class="db-card-val">{{ $total_users }}</div>
            <div class="db-card-sub">Registered</div>
            <a href="{{ route('admin.users.index') }}" class="db-card-link">
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        {{-- Products --}}
        <div class="db-card">
            <div class="db-card-ico" style="background:#f2f2f2;">
                <i class="bi bi-box-seam" style="color:#111; font-size:20px;"></i>
            </div>
            <div class="db-card-lbl">Total Products</div>
            <div class="db-card-val">{{ $total_products }}</div>
            <div class="db-card-sub">In catalog</div>
            <a href="{{ route('products.index') }}" class="db-card-link">
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </div>

    {{-- MAIN GRID --}}
    <div class="db-grid">

        {{-- LEFT --}}
        <div>

            {{-- Revenue Chart --}}
            <div class="db-panel">
                <div class="db-ph">
                    <h5>Revenue Overview</h5>
                    <span class="db-ph-sub">Last 7 months</span>
                </div>
                <div style="padding:20px 22px 16px;">
                    <canvas id="revenueChart" style="max-height:170px;"></canvas>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="db-panel">
                <div class="db-ph">
                    <h5>Recent Orders</h5>
                    <a href="{{ route('admin.orders.index') }}" class="db-view-all">View All →</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="db-tbl">
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
                                <td><strong style="color:#111;">#{{ $order->id }}</strong></td>
                                <td>{{ $order->user->name ?? $order->shipping_name ?? 'Guest' }}</td>
                                <td><strong>${{ number_format($order->total, 0) }}</strong></td>
                                <td style="color:#aaa;">{{ ucfirst($order->payment_method ?? '-') }}</td>
                                <td>
                                    @php
                                        $pc = match($order->status) {
                                            'delivered'  => 'sp-delivered',
                                            'shipped'    => 'sp-shipped',
                                            'pending'    => 'sp-pending',
                                            'cancelled'  => 'sp-cancelled',
                                            'processing' => 'sp-processing',
                                            'confirmed'  => 'sp-confirmed',
                                            default      => 'sp-default'
                                        };
                                    @endphp
                                    <span class="sp {{ $pc }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td style="color:#aaa;">{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:#ccc;padding:40px;">No orders yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- RIGHT --}}
        <div>

            {{-- Donut --}}
            <div class="db-panel">
                <div class="db-ph">
                    <h5>Order Status</h5>
                </div>
                <div class="db-donut-wrap">
                    <canvas id="statusChart" width="160" height="160" style="max-width:160px;"></canvas>
                    <div class="db-donut-mid">
                        <div class="db-donut-num">{{ $total_orders }}</div>
                        <div class="db-donut-tag">Total</div>
                    </div>
                </div>
                <div class="db-legend">
                    @php $dc = ['#111','#555','#888','#bbb','#ccc','#000']; @endphp
                    @foreach($status_data as $i => $s)
                    <div class="db-leg-row">
                        <div class="db-leg-left">
                            <span class="db-leg-dot" style="background:{{ $dc[$i % 6] }};"></span>
                            {{ ucfirst($s->status) }}
                        </div>
                        <div class="db-leg-num">{{ $s->count }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="db-panel">
                <div class="db-ph"><h5>Quick Actions</h5></div>
                <div class="db-qa-list">
                    <a href="{{ route('admin.orders.index') }}" class="db-qa">
                        <div class="db-qa-ico"><i class="bi bi-bag-check"></i></div>
                        All Orders
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="db-qa">
                        <div class="db-qa-ico"><i class="bi bi-people-fill"></i></div>
                        Manage Users
                    </a>
                    <a href="{{ route('products.index') }}" class="db-qa">
                        <div class="db-qa-ico"><i class="bi bi-box-seam"></i></div>
                        Products
                    </a>
                    <a href="{{ route('admin.placeorder') }}" class="db-qa">
                        <div class="db-qa-ico"><i class="bi bi-ui-checks-grid"></i></div>
                        Place Orders
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="db-qa">
                        <div class="db-qa-ico"><i class="bi bi-credit-card"></i></div>
                        Payments
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
    const labels  = @json($chart_labels);
    const revenue = @json($chart_revenue);

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: revenue,
                borderColor: '#111',
                backgroundColor: 'rgba(0,0,0,0.06)',
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#111',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.42
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: { label: c => '  $' + c.parsed.y.toLocaleString() }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f2f2f2', drawBorder: false },
                    ticks: { color: '#bbb', font: { size: 12, family: 'DM Sans' }, padding: 8 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#bbb', font: { size: 12, family: 'DM Sans' }, padding: 4 }
                }
            }
        }
    });

    const sLabels = @json($status_data->pluck('status'));
    const sCounts = @json($status_data->pluck('count'));

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: sLabels,
            datasets: [{
                data: sCounts,
                backgroundColor: ['#111','#444','#777','#aaa','#ccc','#000'],
                borderWidth: 4,
                borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    padding: 12,
                    cornerRadius: 10
                }
            },
            cutout: '68%'
        }
    });
})();
</script>

@endsection
