@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <h1 class="mt-4" style="font-size:30px;font-weight:700;">All Orders</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active" style="font-size:15px;">Orders</li>
  </ol>

  {{-- ─── STATUS FILTER TABS ─── --}}
  <div class="order-status-tabs mb-4">
    @php
      $statuses = [
        'all'        => 'All',
        'new'        => 'New',
        'confirmed'  => 'Confirmed',
        'production' => 'Production',
        'shipped'    => 'Shipped',
        'delivered'  => 'Delivered',
        'cancelled'  => 'Cancelled',
      ];
      $statusColors = [
        'all'        => '#6c757d',
        'new'        => '#8b5cf6',
        'confirmed'  => '#3b82f6',
        'production' => '#f59e0b',
        'shipped'    => '#0ea5e9',
        'delivered'  => '#10b981',
        'cancelled'  => '#ef4444',
      ];
      $tabData = [
        'all'        => ['count' => $orders->count(),                              'total' => $orders->sum('total')],
        'new'        => ['count' => $orders->where('status','new')->count(),        'total' => $orders->where('status','new')->sum('total')],
        'confirmed'  => ['count' => $orders->where('status','confirmed')->count(),  'total' => $orders->where('status','confirmed')->sum('total')],
        'production' => ['count' => $orders->where('status','production')->count(), 'total' => $orders->where('status','production')->sum('total')],
        'shipped'    => ['count' => $orders->where('status','shipped')->count(),    'total' => $orders->where('status','shipped')->sum('total')],
        'delivered'  => ['count' => $orders->where('status','delivered')->count(),  'total' => $orders->where('status','delivered')->sum('total')],
        'cancelled'  => ['count' => $orders->where('status','cancelled')->count(),  'total' => $orders->where('status','cancelled')->sum('total')],
      ];
    @endphp

    @foreach($statuses as $key => $label)
    <button
      class="status-tab {{ $key === 'all' ? 'active' : '' }}"
      data-filter="{{ $key }}"
      style="--tab-color: {{ $statusColors[$key] }}"
      onclick="filterByStatus('{{ $key }}')"
    >
      <span class="tab-dot"></span>
      <span class="tab-label">{{ $label }}</span>
      <span class="tab-right">
        <span class="tab-count">{{ $tabData[$key]['count'] }} orders</span>
        <span class="tab-amount">${{ number_format($tabData[$key]['total'], 2) }}</span>
      </span>
    </button>
    @endforeach
  </div>

  {{-- ─── MAIN CARD ─── --}}
  <div class="card mb-4 orders-card">
    <div class="card-header">
      <i class="fas fa-table me-1"></i> Orders List
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="ordersTable">
          <thead>
            <tr>
              <th>#ID</th>
              <th>User</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>City</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
            <tr class="order-row" data-status="{{ $order->status }}">

              <td class="fw-semibold text-muted">#{{ $order->id }}</td>

              <td>
                @if($order->user)
                  <span class="fw-semibold">{{ $order->user->name ?? 'N/A' }}</span><br>
                  <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                @else
                  <span class="badge bg-warning text-dark">Guest</span>
                @endif
              </td>

              <td class="fw-bold">${{ number_format($order->total, 2) }}</td>

              <td>
                <span class="payment-badge">{{ ucfirst($order->payment_method) }}</span>
              </td>

              <td>
                @php
                  $sc = match($order->status) {
                    'new'        => 'status-new',
                    'confirmed'  => 'status-confirmed',
                    'production' => 'status-production',
                    'shipped'    => 'status-shipped',
                    'delivered'  => 'status-delivered',
                    'cancelled'  => 'status-cancelled',
                    default      => 'status-default',
                  };
                @endphp
                <span class="order-status-badge {{ $sc }}">{{ ucfirst($order->status) }}</span>
              </td>

              <td>{{ $order->shipping_name }}</td>
              <td>{{ $order->shipping_phone }}</td>
              <td>{{ $order->shipping_city }}</td>
              <td class="text-muted">
                {{ $order->created_at->format('d M Y') }}<br>
                <small>{{ $order->created_at->format('h:i A') }}</small>
              </td>

              <td>
              <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-view">
  <i class="bi bi-eye"></i> View
</a>

<form action="{{ route('admin.orders.destroy', $order->id) }}"
      method="POST"
      style="display:inline-block"
      onsubmit="return confirm('Delete this order?')">

    @csrf
    @method('DELETE')

    <button type="submit" class="btn-delete">
        <i class="bi bi-trash"></i> Delete
    </button>
</form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ── STYLES ── --}}
<style>
/* ── TABS ── */
.order-status-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.status-tab {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border-radius: 12px;
  border: 2px solid #e5e5e5;
  background: #fff;
  font-size: 17px;
  font-weight: 600;
  color: #555;
  cursor: pointer;
  transition: all .18s;
  text-align: left;
}

.tab-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--tab-color);
  flex-shrink: 0;
}

.tab-label {
  white-space: nowrap;
  min-width: 70px;
}

.tab-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 3px;
  border-left: 1px solid #e5e5e5;
  padding-left: 12px;
  margin-left: 2px;
}

.tab-count {
  font-size: 15px;
  font-weight: 700;
  color: #888;
  white-space: nowrap;
}

.tab-amount {
  font-size: 15px;
  font-weight: 800;
  color: #333;
  white-space: nowrap;
}

.status-tab.active,
.status-tab:hover {
  border-color: var(--tab-color);
  background: #fff;
}

.status-tab.active .tab-label,
.status-tab:hover .tab-label {
  color: var(--tab-color);
}

.status-tab.active .tab-right,
.status-tab:hover .tab-right {
  border-left-color: var(--tab-color);
}

.status-tab.active .tab-amount {
  color: var(--tab-color);
}

/* ── CARD ── */
.orders-card {
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
}

.orders-card .card-header {
  background: #fff;
  border-bottom: 1.5px solid #e5e7eb;
  border-radius: 12px 12px 0 0;
  padding: 16px 22px;
  font-size: 18px;
  font-weight: 700;
  color: #111;
}

/* ── TABLE ── */
#ordersTable thead tr th {
  background: #f8f9fa;
  font-size: 15.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .4px;
  color: #6b7280;
  padding: 14px 16px;
  border-bottom: 2px solid #e5e7eb;
  white-space: nowrap;
}

#ordersTable tbody tr td {
  padding: 14px 16px;
  font-size: 17px;
  color: #111;
  vertical-align: middle;
  border-color: #f0f0f0;
}

#ordersTable tbody tr:hover {
  background: #fafafa;
}

/* ── STATUS BADGES ── */
.order-status-badge {
  display: inline-block;
  padding: 4px 13px;
  border-radius: 20px;
  font-size: 15.5px;
  font-weight: 700;
}
.status-new        { background:#ede9fe; color:#5b21b6; }
.status-confirmed  { background:#dbeafe; color:#1e40af; }
.status-production { background:#fef3c7; color:#92400e; }
.status-shipped    { background:#e0f2fe; color:#075985; }
.status-delivered  { background:#d1fae5; color:#065f46; }
.status-cancelled  { background:#fee2e2; color:#991b1b; }
.status-default    { background:#f3f4f6; color:#374151; }

/* ── PAYMENT BADGE ── */
.payment-badge {
  font-size: 15.5px;
  font-weight: 600;
  color: #374151;
  background: #f3f4f6;
  padding: 4px 11px;
  border-radius: 6px;
}

/* ── VIEW BUTTON ── */
.btn-view {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  font-size: 16px;
  font-weight: 600;
  color: #111;
  border: 1.5px solid #d1d5db;
  border-radius: 7px;
  background: #fff;
  text-decoration: none;
  transition: all .15s;
  white-space: nowrap;
}
.btn-view:hover {
  background: #111;
  color: #fff;
  border-color: #111;
}
</style>

{{-- ── JAVASCRIPT ── --}}
<script>
function filterByStatus(status) {
  document.querySelectorAll('.status-tab').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.filter === status)
  })
  document.querySelectorAll('.order-row').forEach(row => {
    row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none'
  })
}
</script>
@endsection
