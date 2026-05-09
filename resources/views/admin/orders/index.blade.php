@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <h1 class="mt-4" style="font-size:30px;font-weight:700;">All Orders</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active" style="font-size:15px;">Orders</li>
  </ol>

  {{-- ─── CATEGORY CARDS ─── --}}
  @php
    $productIds = $orders->flatMap(fn($o) => collect($o->items)->pluck('id'))->unique()->filter();
    $products   = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
    $orderCategories = [];
    foreach($orders as $order) {
      $cats = collect($order->items)
        ->map(fn($item) => $products->get($item['id'])?->category_id)
        ->filter()->unique()->values();
      $orderCategories[$order->id] = $cats;
    }
    $catOrderCount = [];
    foreach($categories as $cat) {
      $catOrderCount[$cat->id] = $orders->filter(
        fn($o) => in_array($cat->id, $orderCategories[$o->id]->toArray())
      )->count();
    }
  @endphp

  <div class="cat-cards-row mb-4">
    <div class="cat-card active" data-cat="all" onclick="filterByCat(this,'all')">
      <div class="cat-card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
      <div class="cat-card-name">All</div>
      <div class="cat-card-count">{{ $orders->count() }} orders</div>
    </div>
    @foreach($categories as $cat)
      @if(($catOrderCount[$cat->id] ?? 0) === 0) @continue @endif
      <div class="cat-card" data-cat="{{ $cat->id }}" onclick="filterByCat(this,'{{ $cat->id }}')">
        @if($cat->icon_image)
          <div class="cat-card-thumb"><img src="{{ $cat->icon_image }}" alt="{{ $cat->name }}"></div>
        @else
          <div class="cat-card-icon"><i class="bi bi-tag-fill"></i></div>
        @endif
        <div class="cat-card-name">{{ $cat->name }}</div>
        <div class="cat-card-count">{{ $catOrderCount[$cat->id] }} orders</div>
      </div>
    @endforeach
  </div>

  {{-- ─── BULK ACTION BAR ─── --}}
  <div id="bulkBar" style="display:none; position:sticky; top:70px; z-index:999; background:#0a0a0a; color:#fff; border-radius:12px; padding:14px 20px; margin-bottom:16px; align-items:center; gap:16px; flex-wrap:wrap; box-shadow:0 4px 20px rgba(0,0,0,.2);">
    <span id="bulkCount" style="font-size:14px; font-weight:600;">0 selected</span>
    <select id="bulkStatus" style="padding:8px 14px; border-radius:8px; border:none; font-size:14px; font-weight:600; background:#fff; color:#111; cursor:pointer;">
      <option value="">— Change Status —</option>
      <option value="new">New</option>
      <option value="confirmed">Confirmed</option>
      <option value="production">Production</option>
      <option value="shipped">Shipped</option>
      <option value="delivered">Delivered</option>
      <option value="cancelled">Cancelled</option>
    </select>
    <button onclick="applyBulkStatus()" style="padding:8px 20px; background:#fff; color:#111; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer;">
      ✓ Apply
    </button>
    <button onclick="clearSelection()" style="padding:8px 16px; background:transparent; color:#aaa; border:1px solid #444; border-radius:8px; font-size:13px; cursor:pointer;">
      ✕ Clear
    </button>
    <button onclick="downloadBulkPdf()" style="padding:8px 20px; background:#e0e7ff; color:#3730a3; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer;">
      ↓ Download PDF
    </button>
  </div>

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
              <th style="width:40px;">
                <input type="checkbox" id="selectAll" onclick="toggleAll(this)"
                  style="width:18px;height:18px;cursor:pointer;accent-color:#000;">
              </th>
              <th>#ID</th>
              <th>Order #</th>
              <th>Items</th>
              <th>User</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>City</th>
              <th>Courier Tracking</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
            <tr
              class="order-row"
              data-status="{{ $order->status }}"
              data-cats="{{ implode(',', $orderCategories[$order->id]->toArray()) }}"
            >
              <td>
                <input type="checkbox" class="order-cb" value="{{ $order->id }}"
                  style="width:18px;height:18px;cursor:pointer;accent-color:#000;">
              </td>

              <td class="fw-semibold text-muted">#{{ $order->id }}</td>

              <td>
                @if($order->order_number)
                  <span class="order-num-copy" onclick="copyText('{{ $order->order_number }}')" title="Click to copy">
                    {{ $order->order_number }}
                    <i class="bi bi-clipboard ms-1" style="font-size:11px;"></i>
                  </span>
                @else
                  <span class="text-muted" style="font-size:13px;">—</span>
                @endif
              </td>

              {{-- ✅ ITEMS — sirf images, 4 per row grid --}}
              <td>
                <div class="items-img-grid">
                  @foreach($order->items as $item)
                    @if(!empty($item['image']))
                      <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="items-img-thumb" title="{{ $item['name'] }} | Size: {{ $item['size'] ?? '—' }} | Qty: {{ $item['quantity'] }}">
                    @else
                      <div class="items-img-placeholder" title="{{ $item['name'] }}">
                        <i class="bi bi-image" style="font-size:16px;color:#ccc;"></i>
                      </div>
                    @endif
                  @endforeach
                </div>
              </td>

              <td>
                @if($order->user)
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

              <td>
                @if($order->tracking_number)
                  <span class="tracking-copy" onclick="copyText('{{ $order->tracking_number }}')" title="Click to copy">
                    {{ $order->tracking_number }}
                    <i class="bi bi-clipboard ms-1" style="font-size:12px;"></i>
                  </span>
                @else
                  <span class="text-muted" style="font-size:13px;">—</span>
                @endif
              </td>

              <td class="text-muted">
                {{ $order->created_at->format('d M Y') }}<br>
                <small>{{ $order->created_at->format('h:i A') }}</small>
              </td>

              <td>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-view">
                  <i class="bi bi-eye"></i> View
                </a>
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
/* ── Category Cards ── */
.cat-cards-row { display:flex; gap:14px; overflow-x:auto; padding-bottom:8px; scrollbar-width:thin; }
.cat-card { flex:0 0 120px; background:#fff; border:2px solid #e0e0e0; border-radius:14px; padding:12px 10px; text-align:center; cursor:pointer; transition:all .25s ease; user-select:none; }
.cat-card:hover { border-color:#000; transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,.12); }
.cat-card.active { background:#000; border-color:#000; color:#fff; }
.cat-card-icon { font-size:24px; margin-bottom:5px; line-height:1; }
.cat-card-thumb { width:40px; height:40px; margin:0 auto 6px; border-radius:8px; overflow:hidden; border:1px solid rgba(0,0,0,.1); }
.cat-card-thumb img { width:100%; height:100%; object-fit:cover; }
.cat-card.active .cat-card-thumb { border-color:rgba(255,255,255,.3); }
.cat-card-name { font-size:12px; font-weight:700; margin-bottom:3px; line-height:1.3; }
.cat-card-count { font-size:11px; opacity:.6; }

/* ── Status Tabs ── */
.order-status-tabs { display:flex; flex-wrap:wrap; gap:10px; }
.status-tab { display:inline-flex; align-items:center; gap:10px; padding:12px 18px; border-radius:12px; border:2px solid #e5e5e5; background:#fff; font-size:17px; font-weight:600; color:#555; cursor:pointer; transition:all .18s; text-align:left; }
.tab-dot { width:10px; height:10px; border-radius:50%; background:var(--tab-color); flex-shrink:0; }
.tab-label { white-space:nowrap; min-width:70px; }
.tab-right { display:flex; flex-direction:column; align-items:flex-end; gap:3px; border-left:1px solid #e5e5e5; padding-left:12px; margin-left:2px; }
.tab-count { font-size:15px; font-weight:700; color:#888; white-space:nowrap; }
.tab-amount { font-size:15px; font-weight:800; color:#333; white-space:nowrap; }
.status-tab.active,.status-tab:hover { border-color:var(--tab-color); }
.status-tab.active .tab-label,.status-tab:hover .tab-label { color:var(--tab-color); }
.status-tab.active .tab-right,.status-tab:hover .tab-right { border-left-color:var(--tab-color); }
.status-tab.active .tab-amount { color:var(--tab-color); }

/* ── Orders Card ── */
.orders-card { border:1.5px solid #e5e7eb; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
.orders-card .card-header { background:#fff; border-bottom:1.5px solid #e5e7eb; border-radius:12px 12px 0 0; padding:16px 22px; font-size:18px; font-weight:700; color:#111; }
#ordersTable thead tr th { background:#f8f9fa; font-size:15.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#6b7280; padding:14px 16px; border-bottom:2px solid #e5e7eb; white-space:nowrap; }
#ordersTable tbody tr td { padding:12px 14px; font-size:15px; color:#111; vertical-align:middle; border-color:#f0f0f0; }
#ordersTable tbody tr:hover { background:#fafafa; }
tr.selected-row { background:#f0f4ff !important; }

/* ── Items Image Grid ── */
.items-img-grid {
  display:flex;
  flex-wrap:wrap;
  gap:4px;
  max-width:176px; /* 4 * 40px + 3 * 4px gap = 172px */
}
.items-img-thumb {
  width:40px;
  height:40px;
  object-fit:cover;
  border-radius:6px;
  border:1.5px solid #e5e7eb;
  background:#f8f9fa;
  cursor:default;
  transition:transform .15s;
}
.items-img-thumb:hover {
  transform:scale(1.15);
  z-index:10;
  position:relative;
  box-shadow:0 4px 12px rgba(0,0,0,.15);
}
.items-img-placeholder {
  width:40px;
  height:40px;
  border-radius:6px;
  border:1.5px solid #e5e7eb;
  background:#f8f9fa;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* ── Order Number ── */
.order-num-copy { display:inline-flex; align-items:center; gap:5px; font-family:'Courier New',monospace; font-size:13px; font-weight:700; background:#eef2ff; padding:5px 10px; border-radius:6px; cursor:pointer; color:#3730a3; border:1px solid #c7d2fe; transition:background .15s,border-color .15s; white-space:nowrap; user-select:none; }
.order-num-copy:hover { background:#e0e7ff; border-color:#6366f1; color:#4338ca; }

/* ── Status Badges ── */
.order-status-badge { display:inline-block; padding:4px 13px; border-radius:20px; font-size:13px; font-weight:700; }
.status-new        { background:#ede9fe; color:#5b21b6; }
.status-confirmed  { background:#dbeafe; color:#1e40af; }
.status-production { background:#fef3c7; color:#92400e; }
.status-shipped    { background:#e0f2fe; color:#075985; }
.status-delivered  { background:#d1fae5; color:#065f46; }
.status-cancelled  { background:#fee2e2; color:#991b1b; }
.status-default    { background:#f3f4f6; color:#374151; }
.payment-badge { font-size:13px; font-weight:600; color:#374151; background:#f3f4f6; padding:4px 11px; border-radius:6px; }

/* ── Tracking ── */
.tracking-copy { display:inline-flex; align-items:center; gap:5px; font-family:'Courier New',monospace; font-size:12px; background:#f3f4f6; padding:5px 10px; border-radius:6px; cursor:pointer; color:#111; border:1px solid #e5e7eb; transition:background .15s,border-color .15s,color .15s; white-space:nowrap; user-select:none; }
.tracking-copy:hover { background:#e0e7ff; border-color:#6366f1; color:#4338ca; }

/* ── View Button ── */
.btn-view { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; font-size:14px; font-weight:600; color:#111; border:1.5px solid #d1d5db; border-radius:7px; background:#fff; text-decoration:none; transition:all .15s; white-space:nowrap; }
.btn-view:hover { background:#111; color:#fff; border-color:#111; }
</style>

{{-- ── JAVASCRIPT ── --}}
<script>
  let activeCat    = 'all';
  let activeStatus = 'all';

  function applyFilters() {
    document.querySelectorAll('.order-row').forEach(row => {
      const statusOk = activeStatus === 'all' || row.dataset.status === activeStatus;
      const rowCats  = row.dataset.cats ? row.dataset.cats.split(',') : [];
      const catOk    = activeCat === 'all' || rowCats.includes(String(activeCat));
      row.style.display = (statusOk && catOk) ? '' : 'none';
    });
  }

  function filterByStatus(status) {
    activeStatus = status;
    document.querySelectorAll('.status-tab').forEach(btn =>
      btn.classList.toggle('active', btn.dataset.filter === status)
    );
    applyFilters();
  }

  function filterByCat(card, cat) {
    activeCat = String(cat);
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
    applyFilters();
  }

  function toggleAll(master) {
    document.querySelectorAll('.order-cb').forEach(cb => {
      const row = cb.closest('tr');
      if (row.style.display !== 'none') {
        cb.checked = master.checked;
        row.classList.toggle('selected-row', master.checked);
      }
    });
    updateBulkBar();
  }

  function updateBulkBar() {
    const checked = getChecked();
    const bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = checked.length + ' selected';
    bar.style.display = checked.length > 0 ? 'flex' : 'none';
    const visible = Array.from(document.querySelectorAll('.order-cb'))
      .filter(cb => cb.closest('tr').style.display !== 'none');
    const sa = document.getElementById('selectAll');
    sa.indeterminate = checked.length > 0 && checked.length < visible.length;
    sa.checked = visible.length > 0 && checked.length === visible.length;
  }

  function getChecked() {
    return Array.from(document.querySelectorAll('.order-cb:checked')).map(cb => cb.value);
  }

  function clearSelection() {
    document.querySelectorAll('.order-cb').forEach(cb => {
      cb.checked = false;
      cb.closest('tr').classList.remove('selected-row');
    });
    const sa = document.getElementById('selectAll');
    sa.checked = false;
    sa.indeterminate = false;
    updateBulkBar();
  }

  async function applyBulkStatus() {
    const ids    = getChecked();
    const status = document.getElementById('bulkStatus').value;
    if (!ids.length)  { showToast('⚠ No orders selected!', '#f59e0b'); return; }
    if (!status)      { showToast('⚠ Please select a status!', '#f59e0b'); return; }
    if (!confirm('Are you sure you want to change ' + ids.length + ' order(s) status to "' + status + '"?')) return;

    try {
      const res = await fetch('{{ route("admin.orders.bulkStatus") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ids, status })
      });
      const data = await res.json();
      if (data.success) {
        const statusClasses = {
          new:'status-new', confirmed:'status-confirmed', production:'status-production',
          shipped:'status-shipped', delivered:'status-delivered', cancelled:'status-cancelled'
        };
        ids.forEach(id => {
          const cb = document.querySelector(`.order-cb[value="${id}"]`);
          if (!cb) return;
          const row = cb.closest('tr');
          row.dataset.status = status;
          const badge = row.querySelector('.order-status-badge');
          if (badge) {
            badge.className = 'order-status-badge ' + (statusClasses[status] || 'status-default');
            badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
          }
        });
        clearSelection();
        applyFilters();
        showToast('✓ ' + ids.length + ' orders updated!', '#10b981');
      } else {
        showToast('✕ Error: ' + (data.message || 'Something went wrong'), '#ef4444');
      }
    } catch(e) {
      showToast('✕ Network error!', '#ef4444');
    }
  }

  function downloadBulkPdf() {
    const ids = getChecked();
    if (!ids.length) { showToast('⚠ No orders selected!', '#f59e0b'); return; }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.orders.downloadPdf") }}';
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
    ids.forEach(id => {
      const inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
      form.appendChild(inp);
    });
    document.body.appendChild(form);
    form.submit();
    setTimeout(() => document.body.removeChild(form), 1000);
  }

  function showToast(msg, bg) {
    let t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = `position:fixed;bottom:24px;right:24px;background:${bg||'#111'};color:#fff;padding:12px 22px;border-radius:10px;font-size:14px;font-weight:600;z-index:99999;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:opacity .4s;`;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; }, 2000);
    setTimeout(() => t.remove(), 2400);
  }

  function copyText(text) {
    navigator.clipboard.writeText(text).then(() => showToast('✓ Copied: ' + text, '#111'));
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.order-cb').forEach(cb => {
      cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('selected-row', this.checked);
        updateBulkBar();
      });
    });
  });
</script>
@endsection
