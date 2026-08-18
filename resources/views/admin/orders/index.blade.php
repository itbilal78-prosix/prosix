@extends('layouts.dashboard')

@section('content')

<div class="container-fluid">

  <h1 class="mt-4" style="font-size:34px;font-weight:800;">All Orders</h1>

  <ol class="breadcrumb mb-4">

    <li class="breadcrumb-item active" style="font-size:15px;">Orders</li>

  </ol>

  {{-- ─── CATEGORY CARDS ─── --}}

  @php

    $productIds = $orders

      ->flatMap(function ($order) {

        return collect($order->items ?? [])->pluck('id');

      })

      ->unique()

      ->filter();

    $products = \App\Models\Product::whereIn('id', $productIds)

      ->get()

      ->keyBy('id');

    $orderCategories = [];

    foreach ($orders as $order) {

      $cats = collect($order->items ?? [])

        ->map(function ($item) use ($products) {

          $item = is_object($item) ? (array) $item : $item;

          $productId = $item['id'] ?? null;

          return $productId && $products->has($productId)

            ? $products->get($productId)->category_id

            : null;

        })

        ->filter()

        ->unique()

        ->values();

      $orderCategories[$order->id] = $cats;

    }

    $catOrderCount = [];

    foreach ($categories as $cat) {

      $catOrderCount[$cat->id] = $orders

        ->filter(function ($order) use ($cat, $orderCategories) {

          $cats = $orderCategories[$order->id] ?? collect();

          return in_array($cat->id, $cats->toArray(), true);

        })

        ->count();

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

    <select id="bulkStatus" onchange="toggleBulkCustomStatus(this)" style="padding:8px 14px; border-radius:8px; border:none; font-size:14px; font-weight:600; background:#fff; color:#111; cursor:pointer;">

      <option value="">— Change Status —</option>

      <option value="new">New</option>

      <option value="confirmed">Confirmed</option>

      <option value="production">Production</option>

      <option value="shipped">Shipped</option>

      <option value="delivered">Delivered</option>

      <option value="cancelled">Cancelled</option>

      <option value="__custom__">Custom Status...</option>

    </select>

    <input

      type="text"

      id="bulkCustomStatus"

      placeholder="Type custom status"

      style="display:none;padding:8px 12px;border-radius:8px;border:none;min-width:180px;"

    >

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

  {{-- ─── STATUS FILTER TABS — COUNTS CHANGE WITH SELECTED CATEGORY ─── --}}

  @php

    $standardStatuses = [

      'new'        => 'New',

      'confirmed'  => 'Confirmed',

      'production' => 'Production',

      'shipped'    => 'Shipped',

      'delivered'  => 'Delivered',

      'cancelled'  => 'Cancelled',

    ];

    $customStatuses = $orders

      ->pluck('status')

      ->filter()

      ->map(fn($s) => strtolower(trim($s)))

      ->reject(fn($s) => array_key_exists($s, $standardStatuses))

      ->unique()

      ->values();

    $statuses = ['all' => 'All'] + $standardStatuses;

    foreach ($customStatuses as $customStatus) {

      $statuses[$customStatus] = ucwords(str_replace(['_', '-'], ' ', $customStatus));

    }

    $statusColors = [

      'all'        => '#6c757d',

      'new'        => '#8b5cf6',

      'confirmed'  => '#3b82f6',

      'production' => '#f59e0b',

      'shipped'    => '#0ea5e9',

      'delivered'  => '#10b981',

      'cancelled'  => '#ef4444',

    ];

  @endphp

  <div class="order-status-tabs mb-4">

    @foreach($statuses as $key => $label)

      <button

        class="status-tab {{ $key === 'all' ? 'active' : '' }}"

        data-filter="{{ $key }}"

        style="--tab-color: {{ $statusColors[$key] ?? '#111827' }}"

        onclick="filterByStatus('{{ $key }}')"

      >

        <span class="tab-dot"></span>

        <span class="tab-label">{{ $label }}</span>

        <span class="tab-right">

          <span class="tab-count" data-status-count="{{ $key }}">0 orders</span>

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





              <th>Status</th>

              <th>Remark</th>

              <th>Agreement</th>

              <th>Customer</th>

              <th>Phone</th>





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

              {{-- ✅ ITEMS — first 3 images + remaining count --}}

              <td>

                @php

                  $orderItems = collect($order->items ?? [])

                    ->map(function ($item) {

                      return is_object($item) ? (array) $item : $item;

                    });

                  $orderImages = $orderItems

                    ->map(function ($item) {

                      return $item['image'] ?? null;

                    })

                    ->filter()

                    ->values()

                    ->all();

                  $visibleImages = array_slice($orderImages, 0, 3);

                  $remainingImages = max(count($orderImages) - 3, 0);

                @endphp

                @if(count($orderImages))

                  <div class="items-img-row">

                    @foreach($visibleImages as $imageIndex => $image)

                      <button

                        type="button"

                        class="items-img-button"

                        onclick='openOrderGallery(@json($orderImages), {{ $imageIndex }})'

                        title="Open image"

                      >

                        <img src="{{ $image }}" alt="Order item" class="items-img-thumb">

                      </button>

                    @endforeach

                    @if($remainingImages > 0)

                      <button

                        type="button"

                        class="items-more-button"

                        onclick='openOrderGallery(@json($orderImages), 3)'

                        title="Show all images"

                      >

                        +{{ $remainingImages }}

                      </button>

                    @endif

                  </div>

                @else

                  <span class="text-muted" style="font-size:12px;">No images</span>

                @endif

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

                @php

                  $normalizedStatus = strtolower(trim($order->status));

                  $standard = ['new','confirmed','production','shipped','delivered','cancelled'];

                  $isCustomStatus = !in_array($normalizedStatus, $standard, true);

                @endphp

                <form

                  id="statusForm{{ $order->id }}"

                  method="POST"

                  action="{{ route('admin.orders.updateStatus', $order->id) }}"

                  class="status-inline-form"

                >

                  @csrf

                  <select

                    class="status-inline-select"

                    onchange="handleRowStatusChange(this, {{ $order->id }})"

                  >

                    <option value="new" {{ $normalizedStatus === 'new' ? 'selected' : '' }}>New</option>

                    <option value="confirmed" {{ $normalizedStatus === 'confirmed' ? 'selected' : '' }}>Confirmed</option>

                    <option value="production" {{ $normalizedStatus === 'production' ? 'selected' : '' }}>Production</option>

                    <option value="shipped" {{ $normalizedStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>

                    <option value="delivered" {{ $normalizedStatus === 'delivered' ? 'selected' : '' }}>Delivered</option>

                    <option value="cancelled" {{ $normalizedStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>

                    <option value="__custom__" {{ $isCustomStatus ? 'selected' : '' }}>Custom...</option>

                  </select>

                  <input

                    type="hidden"

                    name="status"

                    id="statusValue{{ $order->id }}"

                    value="{{ $order->status }}"

                  >

                  <div

                    id="customStatusWrap{{ $order->id }}"

                    class="custom-status-wrap"

                    style="{{ $isCustomStatus ? 'display:flex;' : 'display:none;' }}"

                  >

                    <input

                      type="text"

                      id="customStatusInput{{ $order->id }}"

                      class="custom-status-input"

                      value="{{ $isCustomStatus ? $order->status : '' }}"

                      placeholder="Type status"

                    >

                    <button

                      type="button"

                      class="mini-save-btn"

                      onclick="saveCustomRowStatus({{ $order->id }})"

                    >

                      Save

                    </button>

                  </div>

                </form>

              </td>

              <td>

                <form

                  method="POST"

                  action="{{ route('admin.orders.updateRemark', $order->id) }}"

                  class="remark-inline-form"

                >

                  @csrf

                  <textarea

                    name="remark"

                    rows="2"

                    class="remark-input"

                    placeholder="Write remark..."

                  >{{ $order->remark }}</textarea>

                  <button type="submit" class="remark-save-btn">

                    Save

                  </button>

                </form>

              </td>

              {{-- ✅ AGREEMENT RECORD --}}
              <td>
                @php
                  $agreementPdf = $order->agreement_pdf
                    ?: '/pdf/Prosix_Sports_Terms_and_Conditions.pdf';

                  $agreementUrl = str_starts_with($agreementPdf, 'http')
                    ? $agreementPdf
                    : url($agreementPdf);
                @endphp

                @if($order->terms_accepted)
                  <div class="agreement-card">

                    <div class="agreement-card-top">
                      <div class="agreement-check">
                        <i class="bi bi-check-lg"></i>
                      </div>

                      <div class="agreement-title-wrap">
                        <strong>Agreement Accepted</strong>
                        <span>Verified checkout consent</span>
                      </div>
                    </div>

                    <div class="agreement-info-grid">

                      @if($order->terms_accepted_at)
                        <div class="agreement-info-item">
                          <span class="agreement-info-label">Accepted</span>
                          <strong>
                            {{ $order->terms_accepted_at->format('d M Y') }}
                            <small>{{ $order->terms_accepted_at->format('h:i A') }}</small>
                          </strong>
                        </div>
                      @endif

                      @if($order->agreement_ip)
                        <div class="agreement-info-item">
                          <span class="agreement-info-label">IP Address</span>
                          <strong>{{ $order->agreement_ip }}</strong>
                        </div>
                      @endif

                    </div>

                    @if($order->agreement_version)
                      <div class="agreement-version-row">
                        <i class="bi bi-shield-check"></i>
                        <span>Version {{ $order->agreement_version }}</span>
                      </div>
                    @endif

                    <div class="agreement-actions">
                      <a
                        href="{{ $agreementUrl }}"
                        target="_blank"
                        rel="noopener"
                        class="agreement-action agreement-action-pdf"
                      >
                        <i class="bi bi-file-earmark-pdf"></i>
                        PDF
                      </a>

                      @if($order->agreement_user_agent)
                        <button
                          type="button"
                          class="agreement-action agreement-action-device"
                          onclick='showAgreementDevice(@json($order->agreement_user_agent), @json($order->agreement_acceptance_source ?? "website_checkout"))'
                        >
                          <i class="bi bi-laptop"></i>
                          Device
                        </button>
                      @endif
                    </div>

                  </div>
                @else
                  <div class="agreement-empty">
                    <div class="agreement-empty-icon">
                      <i class="bi bi-dash-lg"></i>
                    </div>

                    <div>
                      <strong>Not Recorded</strong>
                      <span>No agreement data</span>
                    </div>
                  </div>
                @endif
              </td>

              <td>{{ $order->shipping_name }}</td>

              <td>{{ $order->shipping_phone }}</td>





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





{{-- ── ORDER IMAGE GALLERY ── --}}

<div id="orderGalleryModal" class="order-gallery-modal" aria-hidden="true">

  <div class="order-gallery-backdrop" onclick="closeOrderGallery()"></div>

  <div class="order-gallery-dialog">

    <div class="order-gallery-head">

      <div>

        <strong>Order Images</strong>

        <span id="orderGalleryCounter"></span>

      </div>

      <button type="button" class="order-gallery-close" onclick="closeOrderGallery()">×</button>

    </div>

    <div class="order-gallery-main">

      <button type="button" class="order-gallery-nav order-gallery-prev" onclick="galleryPrevious()">‹</button>

      <div class="order-gallery-image-wrap">

        <img id="orderGalleryMainImage" src="" alt="Order image">

      </div>

      <button type="button" class="order-gallery-nav order-gallery-next" onclick="galleryNext()">›</button>

    </div>

    <div id="orderGalleryThumbs" class="order-gallery-thumbs"></div>

  </div>

</div>

<div id="agreementDeviceModal" class="agreement-device-modal" aria-hidden="true">

  <div class="order-gallery-backdrop" onclick="closeAgreementDevice()"></div>

  <div class="agreement-device-dialog">

    <div class="d-flex justify-content-between align-items-center mb-3">

      <h3>Agreement Device Information</h3>

      <button type="button" class="order-gallery-close" onclick="closeAgreementDevice()">×</button>

    </div>

    <div class="agreement-device-info">

      <strong>Source</strong>

      <div id="agreementDeviceSource" class="mb-3"></div>

      <strong>User Agent</strong>

      <div id="agreementDeviceUserAgent"></div>

    </div>

  </div>

</div>

{{-- ── STYLES ── --}}

<style>

/* =========================================================

   PAGE WIDTH / NO OVERLAY

\\========================================================= */

.prosix-orders-page {

  width: 100%;

  max-width: 100%;

  overflow: hidden;

  padding-left: 18px !important;

  padding-right: 18px !important;

}

.orders-card {

  width: 100%;

  max-width: 100%;

  overflow: hidden;

}

.orders-card .card-body {

  width: 100%;

  max-width: 100%;

  overflow: hidden;

}

.orders-card .table-responsive {

  width:100%;

  max-width:100%;

  overflow-x:hidden !important;

  overflow-y:visible;

}





































/* ── Category Cards ── */

.cat-cards-row { display:flex; gap:14px; overflow-x:auto; padding-bottom:8px; scrollbar-width:thin; }

.cat-card { flex:0 0 120px; background:#fff; border:2px solid #e0e0e0; border-radius:14px; padding:12px 10px; text-align:center; cursor:pointer; transition:all .25s ease; user-select:none; }

.cat-card:hover { border-color:#000; transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,.12); }

.cat-card.active { background:#000; border-color:#000; color:#fff; }

.cat-card-icon { font-size:24px; margin-bottom:5px; line-height:1; }

.cat-card-thumb { width:40px; height:40px; margin:0 auto 6px; border-radius:8px; overflow:hidden; border:1px solid rgba(0,0,0,.1); }

.cat-card-thumb img { width:100%; height:100%; object-fit:cover; }

.cat-card.active .cat-card-thumb { border-color:rgba(255,255,255,.3); }

.cat-card-name { font-size:14px; font-weight:700; margin-bottom:3px; line-height:1.3; }

.cat-card-count { font-size:12px; opacity:.6; }

/* ── Status Tabs ── */

.order-status-tabs { display:flex; flex-wrap:wrap; gap:10px; }

.status-tab { display:inline-flex; align-items:center; gap:10px; padding:13px 18px; border-radius:12px; border:2px solid #e5e5e5; background:#fff; font-size:16px; font-weight:600; color:#555; cursor:pointer; transition:all .18s; text-align:left; }

.tab-dot { width:10px; height:10px; border-radius:50%; background:var(--tab-color); flex-shrink:0; }

.tab-label { white-space:nowrap; min-width:70px; }

.tab-right { display:flex; flex-direction:column; align-items:flex-end; gap:3px; border-left:1px solid #e5e5e5; padding-left:12px; margin-left:2px; }

.tab-count { font-size:13px; font-weight:700; color:#888; white-space:nowrap; }

.tab-amount { font-size:15px; font-weight:800; color:#333; white-space:nowrap; }

.status-tab.active,.status-tab:hover { border-color:var(--tab-color); }

.status-tab.active .tab-label,.status-tab:hover .tab-label { color:var(--tab-color); }

.status-tab.active .tab-right,.status-tab:hover .tab-right { border-left-color:var(--tab-color); }

.status-tab.active .tab-amount { color:var(--tab-color); }

/* ── Orders Card ── */

.orders-card { border:1.5px solid #e5e7eb; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.06); }

.orders-card .card-header { background:#fff; border-bottom:1.5px solid #e5e7eb; border-radius:12px 12px 0 0; padding:17px 22px; font-size:20px; font-weight:700; color:#111; }

\#ordersTable thead tr th {

  background:#f8f9fa;

  font-size:12px;

  font-weight:800;

  text-transform:uppercase;

  letter-spacing:.15px;

  color:#555f69;

  padding:13px 8px;

  border-bottom:2px solid #e5e7eb;

  white-space:normal;

  line-height:1.25;

}

\#ordersTable tbody tr td {

  padding:13px 8px;

  font-size:13px;

  line-height:1.45;

  color:#111;

  vertical-align:middle;

  border-color:#f0f0f0;

  background:#fff;

  white-space:normal;

}

\#ordersTable tbody tr:hover { background:#fafafa; }

#ordersTable {

  width:100%;

  min-width:0 !important;

  table-layout:fixed;

  margin:0;

}

#ordersTable thead tr th {

  background:#f8f9fa;

  font-size:13px;

  font-weight:800;

  text-transform:uppercase;

  letter-spacing:.15px;

  color:#555f69;

  padding:14px 7px;

  border-bottom:2px solid #e5e7eb;

  white-space:normal;

  line-height:1.25;

  vertical-align:middle;

}

#ordersTable tbody tr td {

  padding:13px 7px;

  font-size:13px;

  line-height:1.45;

  color:#111;

  vertical-align:middle;

  border-color:#f0f0f0;

  background:#fff;

  white-space:normal;

  overflow-wrap:anywhere;

}

#ordersTable tbody tr:hover {

  background:#fafafa;

}

/*

\|--------------------------------------------------------------------------

\| EXACT 14 COLUMN WIDTHS = 100%

\|--------------------------------------------------------------------------

\| 1 checkbox

\| 2 id

\| 3 order

\| 4 items

\| 5 user

\| 6 total

\| 7 status

\| 8 remark

\| 9 agreement

\| 10 customer

\| 11 phone

\| 12 tracking

\| 13 date

\| 14 action

*/

#ordersTable th:nth-child(1),

#ordersTable td:nth-child(1)  { width:2%; }

#ordersTable th:nth-child(2),

#ordersTable td:nth-child(2)  { width:3%; }

#ordersTable th:nth-child(3),
#ordersTable td:nth-child(3) { width:7%; }

#ordersTable th:nth-child(4),
#ordersTable td:nth-child(4) { width:12%; }

#ordersTable th:nth-child(5),

#ordersTable td:nth-child(5)  { width:8%; }

#ordersTable th:nth-child(6),

#ordersTable td:nth-child(6)  { width:6%; }

#ordersTable th:nth-child(7),

#ordersTable td:nth-child(7)  { width:9%; }

#ordersTable th:nth-child(8),
#ordersTable td:nth-child(8) { width:10%; }

#ordersTable th:nth-child(9),
#ordersTable td:nth-child(9) { width:12%; }

#ordersTable th:nth-child(10),
#ordersTable td:nth-child(10) { width:8%; }

#ordersTable th:nth-child(11),
#ordersTable td:nth-child(11) { width:7%; }

#ordersTable th:nth-child(12),
#ordersTable td:nth-child(12) { width:4%; }

#ordersTable th:nth-child(13),
#ordersTable td:nth-child(13) { width:5%; }

#ordersTable th:nth-child(14),

#ordersTable td:nth-child(14) { width:4%; }

#ordersTable th,

#ordersTable td {

  min-width:0 !important;

  max-width:none !important;

  box-sizing:border-box;

}

tr.selected-row {

  background:#f0f4ff !important;

}

/* ── Items Images: 3 + +N ── */

.items-img-row {

  display:flex;

  align-items:center;

  gap:4px;

  width:100%;

  min-width:0;

  max-width:100%;

  white-space:nowrap;

  overflow:hidden;

}

.items-img-button,

.items-more-button {

  width:34px;

  height:34px;

  flex:0 0 34px;

  border-radius:7px;

}

.items-img-button { padding:0; overflow:hidden; border:1.5px solid #e5e7eb; background:#fff; cursor:zoom-in; transition:all .18s ease; }

.items-img-button:hover { border-color:#111; transform:translateY(-2px); box-shadow:0 7px 18px rgba(0,0,0,.12); }

.items-img-thumb { width:100%; height:100%; display:block; object-fit:contain; background:#fff; }

.items-more-button { border:none; background:#111; color:#fff; font-size:13px; font-weight:800; cursor:pointer; transition:all .18s ease; }

.items-more-button:hover { background:#2a2a2a; transform:translateY(-2px); }

/* =========================================================
   AGREEMENT - PROFESSIONAL COMPACT CARD
========================================================= */

.agreement-card {
  width:100%;
  max-width:210px;
  min-width:0;
  padding:11px;
  border:1px solid #e5e7eb;
  border-radius:11px;
  background:#ffffff;
  box-shadow:0 3px 10px rgba(15,23,42,.04);
}

.agreement-card-top {
  display:flex;
  align-items:center;
  gap:8px;
  padding-bottom:9px;
  border-bottom:1px solid #eef0f2;
}

.agreement-check {
  width:28px;
  height:28px;
  flex:0 0 28px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:8px;
  background:#ecfdf3;
  color:#078a50;
  font-size:15px;
  font-weight:900;
}

.agreement-title-wrap {
  min-width:0;
}

.agreement-title-wrap strong {
  display:block;
  color:#111827;
  font-size:11px;
  font-weight:800;
  line-height:1.25;
  white-space:nowrap;
}

.agreement-title-wrap span {
  display:block;
  margin-top:2px;
  color:#9aa1aa;
  font-size:8px;
  line-height:1.2;
  white-space:nowrap;
}

.agreement-info-grid {
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:7px;
  margin-top:9px;
}

.agreement-info-item {
  min-width:0;
  padding:7px;
  border-radius:8px;
  background:#f8f9fb;
}

.agreement-info-label {
  display:block;
  margin-bottom:3px;
  color:#9aa1aa;
  font-size:7px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.45px;
}

.agreement-info-item strong {
  display:block;
  color:#2f3742;
  font-size:9px;
  font-weight:800;
  line-height:1.25;
  overflow:hidden;
  text-overflow:ellipsis;
}

.agreement-info-item strong small {
  display:block;
  margin-top:2px;
  color:#7c8590;
  font-size:7px;
  font-weight:600;
}

.agreement-version-row {
  margin-top:8px;
  display:flex;
  align-items:center;
  gap:5px;
  color:#7f8791;
  font-size:8px;
  font-weight:700;
}

.agreement-version-row i {
  color:#4b5563;
}

.agreement-actions {
  display:flex;
  align-items:center;
  gap:6px;
  margin-top:9px;
}

.agreement-action {
  min-width:0;
  flex:1 1 0;
  height:30px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:5px;
  padding:0 7px;
  border:1px solid #e2e5e9;
  border-radius:8px;
  background:#fff;
  font-size:9px;
  font-weight:800;
  line-height:1;
  text-decoration:none;
  cursor:pointer;
  transition:all .18s ease;
}

.agreement-action-pdf {
  color:#b42318;
}

.agreement-action-pdf:hover {
  color:#fff;
  border-color:#b42318;
  background:#b42318;
}

.agreement-action-device {
  color:#20242a;
}

.agreement-action-device:hover {
  color:#fff;
  border-color:#111827;
  background:#111827;
}

.agreement-empty {
  width:100%;
  max-width:190px;
  display:flex;
  align-items:center;
  gap:8px;
  padding:10px;
  border:1px dashed #d9dde2;
  border-radius:10px;
  background:#fafbfc;
}

.agreement-empty-icon {
  width:28px;
  height:28px;
  flex:0 0 28px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:8px;
  background:#f0f2f5;
  color:#8a929c;
}

.agreement-empty strong {
  display:block;
  color:#5f6873;
  font-size:10px;
  font-weight:800;
  line-height:1.2;
}

.agreement-empty span {
  display:block;
  margin-top:2px;
  color:#a1a7af;
  font-size:8px;
}

/* ── Gallery + Device Modals ── */

.order-gallery-modal,.agreement-device-modal { position:fixed; inset:0; z-index:99999; display:none; align-items:center; justify-content:center; padding:24px; }

.agreement-device-modal { z-index:100000; }

.order-gallery-modal.show,.agreement-device-modal.show { display:flex; }

.order-gallery-backdrop { position:absolute; inset:0; background:rgba(0,0,0,.82); backdrop-filter:blur(5px); }

.order-gallery-dialog { position:relative; z-index:2; width:min(980px,96vw); max-height:92vh; overflow:hidden; border-radius:18px; background:#fff; box-shadow:0 35px 110px rgba(0,0,0,.38); }

.order-gallery-head { min-height:60px; padding:0 18px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #eceef1; }

.order-gallery-head strong { display:block; font-size:16px; }

.order-gallery-head span { display:block; margin-top:2px; color:#9299a2; font-size:10px; }

.order-gallery-close { width:38px; height:38px; border:none; border-radius:9px; background:#f3f4f6; color:#111; font-size:25px; line-height:1; cursor:pointer; }

.order-gallery-main { position:relative; min-height:560px; display:flex; align-items:center; justify-content:center; background:#f6f7f9; }

.order-gallery-image-wrap { width:calc(100% - 130px); height:560px; display:flex; align-items:center; justify-content:center; }

.order-gallery-image-wrap img { max-width:100%; max-height:100%; object-fit:contain; }

.order-gallery-nav { position:absolute; top:50%; z-index:3; width:46px; height:46px; transform:translateY(-50%); border:1px solid #dfe2e6; border-radius:50%; background:#fff; color:#111; font-size:27px; cursor:pointer; }

.order-gallery-prev { left:18px; }

.order-gallery-next { right:18px; }

.order-gallery-thumbs { padding:12px; display:flex; gap:8px; overflow-x:auto; border-top:1px solid #eceef1; background:#fff; }

.order-gallery-thumb { width:62px; height:62px; flex:0 0 62px; padding:0; overflow:hidden; border:2px solid transparent; border-radius:8px; background:#fff; cursor:pointer; }

.order-gallery-thumb.active { border-color:#111; }

.order-gallery-thumb img { width:100%; height:100%; object-fit:contain; }

.agreement-device-dialog { position:relative; z-index:2; width:min(620px,94vw); padding:24px; border-radius:16px; background:#fff; box-shadow:0 30px 100px rgba(0,0,0,.35); }

.agreement-device-dialog h3 { margin:0; font-size:20px; }

.agreement-device-info { padding:14px; border-radius:10px; background:#f7f8fa; color:#4b5563; font-size:12px; line-height:1.7; word-break:break-word; }

@media(max-width:768px) {

  .order-gallery-main { min-height:390px; }

  .order-gallery-image-wrap { width:calc(100% - 90px); height:390px; }

}

/* ── Order Number ── */

.order-num-copy { display:inline-flex; align-items:center; gap:5px; font-family:'Courier New',monospace; font-size:12px; font-weight:700; background:#eef2ff; padding:5px 10px; border-radius:6px; cursor:pointer; color:#3730a3; border:1px solid #c7d2fe; transition:background .15s,border-color .15s; white-space:nowrap; user-select:none; }

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

.payment-badge { font-size:12px; font-weight:600; color:#374151; background:#f3f4f6; padding:4px 11px; border-radius:6px; }

/* ── Tracking ── */

.tracking-copy { display:inline-flex; align-items:center; gap:5px; font-family:'Courier New',monospace; font-size:12px; background:#f3f4f6; padding:5px 10px; border-radius:6px; cursor:pointer; color:#111; border:1px solid #e5e7eb; transition:background .15s,border-color .15s,color .15s; white-space:nowrap; user-select:none; }

.tracking-copy:hover { background:#e0e7ff; border-color:#6366f1; color:#4338ca; }

/* ── View Button ── */

.btn-view { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; font-size:14px; font-weight:600; color:#111; border:1.5px solid #d1d5db; border-radius:7px; background:#fff; text-decoration:none; transition:all .15s; white-space:nowrap; }

.btn-view:hover { background:#111; color:#fff; border-color:#111; }

/* ── Editable Status + Remark ── */

.status-inline-form {

  width:100%;

  min-width:0;

}

.status-inline-select {

  width:100%;

  max-width:100%;

  min-width:0;

  padding:7px 6px;

  border:1.5px solid #d1d5db;

  border-radius:7px;

  background:#fff;

  color:#111;

  font-size:12px;

  font-weight:700;

  cursor:pointer;

  outline:none;

}

.status-inline-select:focus { border-color:#111; }

.custom-status-wrap { margin-top:6px; align-items:center; gap:5px; }

.custom-status-input {

  width:115px; padding:6px 7px; border:1px solid #d1d5db;

  border-radius:6px; font-size:11px;

}

.mini-save-btn {

  padding:6px 8px; border:none; border-radius:6px;

  background:#111; color:#fff; font-size:11px; font-weight:800; cursor:pointer;

}

.remark-inline-form {

  width:100%;

  min-width:0;

}

.remark-input {

  width:100%;

  min-width:0;

  max-width:100%;

  resize:vertical;

  padding:7px 7px;

  border:1px solid #d1d5db;

  border-radius:7px;

  background:#fff;

  color:#111;

  font-size:12px;

  line-height:1.4;

  outline:none;

  box-sizing:border-box;

}

.remark-input:focus { border-color:#111; }

.remark-save-btn {

  margin-top:5px; padding:5px 10px; border:none; border-radius:6px;

  background:#111827; color:#fff; font-size:11px; font-weight:800; cursor:pointer;

}





/* =========================================================

   RESPONSIVE

\\========================================================= */

@media (max-width: 1200px) {

  .prosix-orders-page {

    padding-left:12px !important;

    padding-right:12px !important;

  }

  #ordersTable {

    min-width:0 !important;

  }

  #ordersTable thead tr th {

    font-size:12px;

  }

  #ordersTable tbody tr td {

    font-size:13px;

  }

}

@media (max-width: 768px) {

  .prosix-orders-page {

    padding-left:8px !important;

    padding-right:8px !important;

  }

  .status-filter-wrap,

  .order-status-tabs {

    overflow-x:auto;

    flex-wrap:nowrap;

    padding-bottom:7px;

  }

  .status-tab,

  .status-filter {

    flex:0 0 auto;

  }

  #ordersTable {

    min-width:0 !important;

  }

  .items-img-button,

  .items-more-button {

    width:50px;

    height:50px;

    flex-basis:50px;

  }

}





#ordersTable select,

#ordersTable textarea,

#ordersTable input {

  max-width:100%;

}

#ordersTable .status-select,

#ordersTable .remark-input {

  width:100%;

}

#ordersTable .agreement-record {

  width:100%;

}

#ordersTable .order-actions {

  display:flex;

  flex-wrap:wrap;

  gap:5px;

}

.orders-card,

.orders-card .card-body,

.orders-card .table-responsive {

  box-sizing:border-box;

}

body {

  overflow-x:hidden;

}











/* =========================================================

   FINAL ANTI-OVERLAP FIX

\========================================================= */

.orders-card,

.orders-card .card-body,

.orders-card .table-responsive {

  width:100%;

  max-width:100%;

  box-sizing:border-box;

}

.orders-card {

  overflow:hidden !important;

}

.orders-card .table-responsive {

  overflow-x:hidden !important;

  overflow-y:visible !important;

}

#ordersTable .order-num-copy {

  max-width:100%;

  padding:5px 6px;

  font-size:10px;

  overflow:hidden;

  text-overflow:ellipsis;

}

#ordersTable td:nth-child(5) small,

#ordersTable td:nth-child(10),

#ordersTable td:nth-child(11) {

  word-break:break-word;

  overflow-wrap:anywhere;

}

#ordersTable .btn-view {

  width:100%;

  max-width:100%;

  justify-content:center;

  padding:7px 5px;

  font-size:12px;

}

#ordersTable .tracking-copy {

  max-width:100%;

  overflow:hidden;

  text-overflow:ellipsis;

  padding:5px 5px;

  font-size:10px;

}

#ordersTable .remark-save-btn {

  font-size:10px;

  padding:5px 8px;

}

#ordersTable .custom-status-input {

  width:100%;

  max-width:100%;

  min-width:0;

}

#ordersTable .custom-status-wrap {

  width:100%;

  flex-wrap:wrap;

}

body {

  overflow-x:hidden !important;

}

/* Only on truly small screens, scroll inside the CARD — never the whole page */

@media (max-width: 900px) {

  .orders-card .table-responsive {

    overflow-x:auto !important;

  }

  #ordersTable {

    min-width:1120px !important;

    table-layout:auto;

  }

}

</style>

{{-- ── JAVASCRIPT ── --}}

<script>

  let orderGalleryImages = [];

  let orderGalleryIndex = 0;

  function openOrderGallery(images, startIndex = 0) {

    orderGalleryImages = Array.isArray(images) ? images.filter(Boolean) : [];

    if (!orderGalleryImages.length) return;

    orderGalleryIndex = Math.min(Math.max(Number(startIndex) || 0, 0), orderGalleryImages.length - 1);

    const modal = document.getElementById('orderGalleryModal');

    modal.classList.add('show');

    modal.setAttribute('aria-hidden', 'false');

    document.body.style.overflow = 'hidden';

    renderOrderGallery();

  }

  function closeOrderGallery() {

    const modal = document.getElementById('orderGalleryModal');

    modal.classList.remove('show');

    modal.setAttribute('aria-hidden', 'true');

    document.body.style.overflow = '';

  }

  function galleryPrevious() {

    if (!orderGalleryImages.length) return;

    orderGalleryIndex = (orderGalleryIndex - 1 + orderGalleryImages.length) % orderGalleryImages.length;

    renderOrderGallery();

  }

  function galleryNext() {

    if (!orderGalleryImages.length) return;

    orderGalleryIndex = (orderGalleryIndex + 1) % orderGalleryImages.length;

    renderOrderGallery();

  }

  function setOrderGalleryIndex(index) {

    orderGalleryIndex = index;

    renderOrderGallery();

  }

  function renderOrderGallery() {

    const image = document.getElementById('orderGalleryMainImage');

    const counter = document.getElementById('orderGalleryCounter');

    const thumbs = document.getElementById('orderGalleryThumbs');

    if (!image || !counter || !thumbs || !orderGalleryImages.length) return;

    image.src = orderGalleryImages[orderGalleryIndex];

    counter.textContent = `${orderGalleryIndex + 1} of ${orderGalleryImages.length}`;

    thumbs.innerHTML = '';

    orderGalleryImages.forEach((src, index) => {

      const button = document.createElement('button');

      button.type = 'button';

      button.className = 'order-gallery-thumb' + (index === orderGalleryIndex ? ' active' : '');

      button.onclick = () => setOrderGalleryIndex(index);

      const img = document.createElement('img');

      img.src = src;

      img.alt = `Order image ${index + 1}`;

      button.appendChild(img);

      thumbs.appendChild(button);

    });

  }

  function showAgreementDevice(userAgent, source) {

    document.getElementById('agreementDeviceUserAgent').textContent = userAgent || 'Not available';

    document.getElementById('agreementDeviceSource').textContent = source || 'website_checkout';

    const modal = document.getElementById('agreementDeviceModal');

    modal.classList.add('show');

    modal.setAttribute('aria-hidden', 'false');

    document.body.style.overflow = 'hidden';

  }

  function closeAgreementDevice() {

    const modal = document.getElementById('agreementDeviceModal');

    modal.classList.remove('show');

    modal.setAttribute('aria-hidden', 'true');

    const gallery = document.getElementById('orderGalleryModal');

    if (!gallery.classList.contains('show')) document.body.style.overflow = '';

  }





  let activeCat    = 'all';

  let activeStatus = 'all';

  function normalizedStatus(value) {

    return String(value || '').trim().toLowerCase();

  }

  function updateStatusCountsForActiveCategory() {

    const rows = Array.from(document.querySelectorAll('.order-row'));

    document.querySelectorAll('[data-status-count]').forEach(counter => {

      const status = counter.dataset.statusCount;

      const count = rows.filter(row => {

        const rowCats = row.dataset.cats ? row.dataset.cats.split(',') : [];

        const catOk = activeCat === 'all' || rowCats.includes(String(activeCat));

        const statusOk =

          status === 'all' ||

          normalizedStatus(row.dataset.status) === normalizedStatus(status);

        return catOk && statusOk;

      }).length;

      counter.textContent = count + (count === 1 ? ' order' : ' orders');

    });

  }

  function applyFilters() {

    document.querySelectorAll('.order-row').forEach(row => {

      const statusOk =

        activeStatus === 'all' ||

        normalizedStatus(row.dataset.status) === normalizedStatus(activeStatus);

      const rowCats = row.dataset.cats ? row.dataset.cats.split(',') : [];

      const catOk = activeCat === 'all' || rowCats.includes(String(activeCat));

      row.style.display = (statusOk && catOk) ? '' : 'none';

    });

    updateStatusCountsForActiveCategory();

    updateBulkBar();

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

  function handleRowStatusChange(select, orderId) {

    const wrap = document.getElementById('customStatusWrap' + orderId);

    if (select.value === '__custom__') {

      wrap.style.display = 'flex';

      document.getElementById('customStatusInput' + orderId).focus();

      return;

    }

    wrap.style.display = 'none';

    document.getElementById('statusValue' + orderId).value = select.value;

    document.getElementById('statusForm' + orderId).submit();

  }

  function saveCustomRowStatus(orderId) {

    const value = document.getElementById('customStatusInput' + orderId).value.trim();

    if (!value) {

      showToast('⚠ Type a custom status first!', '#f59e0b');

      return;

    }

    document.getElementById('statusValue' + orderId).value = value;

    document.getElementById('statusForm' + orderId).submit();

  }

  function toggleBulkCustomStatus(select) {

    const input = document.getElementById('bulkCustomStatus');

    input.style.display = select.value === '__custom__' ? 'block' : 'none';

    if (select.value === '__custom__') {

      input.focus();

    }

  }

  async function applyBulkStatus() {

    const ids    = getChecked();

    let status = document.getElementById('bulkStatus').value;

    if (status === '__custom__') {

      status = document.getElementById('bulkCustomStatus').value.trim();

    }

    if (!ids.length) { showToast('⚠ No orders selected!', '#f59e0b'); return; }

    if (!status) { showToast('⚠ Please select/type a status!', '#f59e0b'); return; }

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

  document.addEventListener('keydown', function(event) {

    const galleryModal = document.getElementById('orderGalleryModal');

    const deviceModal = document.getElementById('agreementDeviceModal');

    if (galleryModal && galleryModal.classList.contains('show')) {

      if (event.key === 'Escape') closeOrderGallery();

      if (event.key === 'ArrowLeft') galleryPrevious();

      if (event.key === 'ArrowRight') galleryNext();

      return;

    }

    if (deviceModal && deviceModal.classList.contains('show') && event.key === 'Escape') {

      closeAgreementDevice();

    }

  });

  document.addEventListener('DOMContentLoaded', () => {

    updateStatusCountsForActiveCategory();

    document.querySelectorAll('.order-cb').forEach(cb => {

      cb.addEventListener('change', function() {

        this.closest('tr').classList.toggle('selected-row', this.checked);

        updateBulkBar();

      });

    });

  });

</script>

@endsection
