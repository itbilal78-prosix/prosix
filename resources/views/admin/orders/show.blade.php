@extends('layouts.dashboard')

@section('content')

<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

  .od-wrap {
    min-height: 100vh;
    background: #f8f8f8;
    padding: 36px 24px 60px;
    font-family: 'DM Sans', sans-serif;
  }

  /* Breadcrumb */
  .od-breadcrumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: #999; margin-bottom: 28px;
    font-family: 'DM Mono', monospace; letter-spacing: .04em;
  }
  .od-breadcrumb a { color: #555; text-decoration: none; transition: color .2s; }
  .od-breadcrumb a:hover { color: #000; }
  .od-breadcrumb span { color: #ccc; }

  /* Header row */
  .od-header {
    display: flex; align-items: flex-start;
    justify-content: space-between; flex-wrap: wrap;
    gap: 16px; margin-bottom: 32px;
  }
  .od-title {
    font-family: 'Playfair Display', serif;
    font-size: 34px; font-weight: 700;
    color: #0a0a0a; letter-spacing: -.5px; margin: 0;
  }
  .od-title span {
    font-family: 'DM Mono', monospace;
    font-size: 20px; font-weight: 400; color: #aaa;
  }
  .od-status-pill {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 16px; border-radius: 99px;
    font-size: 12px; font-weight: 600; letter-spacing: .06em;
    text-transform: uppercase;
    border: 1.5px solid #111; color: #111; background: #fff;
    font-family: 'DM Mono', monospace;
  }
  .od-status-pill.pending  { border-color: #111; background: #111; color: #fff; }
  .od-status-pill.shipped  { border-color: #111; background: #f5f5f5; }
  .od-status-pill.delivered{ border-color: #000; background: #000; color: #fff; }
  .od-status-pill.cancelled{ border-color: #bbb; color: #999; background: #fafafa; }
  .od-status-dot {
    width: 7px; height: 7px; border-radius: 50%; background: currentColor;
  }

  /* Main grid */
  .od-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
  }
  @media(max-width:900px){ .od-grid { grid-template-columns: 1fr; } }

  /* Card base */
  .od-card {
    background: #fff;
    border: 1.5px solid #e8e8e8;
    border-radius: 16px;
    overflow: hidden;
  }
  .od-card-head {
    padding: 18px 24px;
    border-bottom: 1.5px solid #f0f0f0;
    display: flex; align-items: center; gap: 10px;
  }
  .od-card-head-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: #0a0a0a; display: grid; place-items: center;
  }
  .od-card-head-icon svg { color: #fff; }
  .od-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 15px; font-weight: 600; color: #0a0a0a; margin: 0;
  }

  /* Order Items */
  .od-items-list { padding: 0; }
  .od-item-row {
    display: flex; align-items: center; gap: 18px;
    padding: 20px 24px;
    border-bottom: 1px solid #f5f5f5;
    transition: background .2s;
  }
  .od-item-row:last-child { border-bottom: none; }
  .od-item-row:hover { background: #fafafa; }
  .od-item-img {
    width: 72px; height: 72px; border-radius: 10px;
    object-fit: cover;
    border: 1.5px solid #efefef;
    background: #f5f5f5;
    flex-shrink: 0;
  }
  .od-item-img-placeholder {
    width: 72px; height: 72px; border-radius: 10px;
    background: #f0f0f0; border: 1.5px solid #e8e8e8;
    display: grid; place-items: center; flex-shrink: 0;
  }
  .od-item-info { flex: 1; min-width: 0; }
  .od-item-name {
    font-weight: 600; font-size: 14px; color: #0a0a0a;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 4px;
  }
  .od-item-meta {
    display: flex; gap: 12px; flex-wrap: wrap;
    font-size: 12px; color: #888; font-family: 'DM Mono', monospace;
  }
  .od-item-meta span { display: flex; align-items: center; gap: 4px; }
  .od-item-price {
    font-family: 'DM Mono', monospace;
    font-size: 15px; font-weight: 500; color: #0a0a0a;
    white-space: nowrap;
  }

  /* Right sidebar */
  .od-sidebar { display: flex; flex-direction: column; gap: 20px; }

  /* Customer card */
  .od-customer-body { padding: 22px 24px; }
  .od-avatar {
    width: 52px; height: 52px; border-radius: 50%;
    background: #0a0a0a; display: grid; place-items: center;
    margin-bottom: 14px;
  }
  .od-avatar-letter {
    font-family: 'Playfair Display', serif;
    font-size: 22px; color: #fff; font-weight: 700; line-height: 1;
  }
  .od-cust-name {
    font-family: 'Playfair Display', serif;
    font-size: 18px; font-weight: 700; color: #0a0a0a; margin-bottom: 4px;
  }
  .od-cust-field {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid #f5f5f5;
    font-size: 13px;
  }
  .od-cust-field:last-child { border-bottom: none; padding-bottom: 0; }
  .od-cust-field svg { color: #999; flex-shrink: 0; margin-top: 1px; }
  .od-cust-label { color: #aaa; font-size: 11px; font-family: 'DM Mono', monospace; display: block; margin-bottom: 1px; }
  .od-cust-val { color: #0a0a0a; font-weight: 500; }

  /* Summary card */
  .od-summary-body { padding: 22px 24px; }
  .od-summary-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 0; font-size: 13px; color: #666;
    border-bottom: 1px solid #f5f5f5;
  }
  .od-summary-row:last-child { border-bottom: none; }
  .od-summary-row.total {
    font-size: 16px; font-weight: 700; color: #0a0a0a;
    border-top: 2px solid #0a0a0a; margin-top: 8px; padding-top: 14px;
    border-bottom: none;
  }
  .od-summary-val { font-family: 'DM Mono', monospace; font-weight: 500; color: #0a0a0a; }

  /* Date card */
  .od-date-body { padding: 18px 24px; }
  .od-date-row { display: flex; justify-content: space-between; font-size: 13px; color: #666; padding: 4px 0; }
  .od-date-row span:last-child { font-family: 'DM Mono', monospace; color: #0a0a0a; font-weight: 500; }

  /* Cancel section */
  .od-cancel-card {
    background: #fff;
    border: 1.5px solid #e8e8e8;
    border-radius: 16px; overflow: hidden;
  }
  .od-cancel-body { padding: 22px 24px; }
  .od-cancel-warn {
    display: flex; gap: 10px; align-items: flex-start;
    background: #fafafa; border: 1px solid #f0f0f0;
    border-radius: 10px; padding: 12px 14px; margin-bottom: 16px;
    font-size: 12px; color: #888; line-height: 1.5;
  }
  .od-cancel-warn svg { flex-shrink: 0; color: #aaa; margin-top: 1px; }

  /* Cancel button */
  .od-cancel-btn {
    width: 100%; padding: 13px;
    background: #0a0a0a; color: #fff;
    border: none; border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px; font-weight: 600;
    cursor: pointer; letter-spacing: .04em;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: background .2s, transform .15s;
  }
  .od-cancel-btn:hover { background: #222; transform: translateY(-1px); }
  .od-cancel-btn:active { transform: translateY(0); }
  .od-cancel-btn.disabled-btn {
    background: #f0f0f0; color: #bbb; cursor: not-allowed; pointer-events: none;
  }

  /* Modal */
  .od-modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
  }
  .od-modal-overlay.active { display: flex; }
  .od-modal {
    background: #fff; border-radius: 20px;
    padding: 36px 32px; max-width: 400px; width: 90%;
    box-shadow: 0 24px 60px rgba(0,0,0,.18);
    animation: modalIn .3s cubic-bezier(.34,1.56,.64,1);
  }
  @keyframes modalIn {
    from { opacity:0; transform:scale(.9) translateY(20px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
  }
  .od-modal-icon {
    width: 52px; height: 52px; border-radius: 50%;
    background: #0a0a0a; display: grid; place-items: center; margin-bottom: 18px;
  }
  .od-modal-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 700; color: #0a0a0a; margin-bottom: 8px;
  }
  .od-modal-desc { font-size: 13px; color: #777; line-height: 1.6; margin-bottom: 24px; }
  .od-modal-btns { display: flex; gap: 10px; }
  .od-modal-no {
    flex: 1; padding: 12px; border: 1.5px solid #e0e0e0;
    border-radius: 10px; background: #fff; color: #555;
    font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: border-color .2s;
  }
  .od-modal-no:hover { border-color: #aaa; }
  .od-modal-yes {
    flex: 1; padding: 12px; border: none;
    border-radius: 10px; background: #0a0a0a; color: #fff;
    font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .2s;
  }
  .od-modal-yes:hover { background: #333; }

  /* Back link */
  .od-back {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; color: #888; text-decoration: none;
    font-family: 'DM Mono', monospace; margin-bottom: 20px;
    transition: color .2s;
  }
  .od-back:hover { color: #000; }
</style>

<div class="od-wrap">

  {{-- Breadcrumb --}}
  <div class="od-breadcrumb">
    <a href="{{ route('admin.orders.index') }}">Orders</a>
    <span>›</span>
    <span>Order #{{ $order->id }}</span>
  </div>

  {{-- Header --}}
  <div class="od-header">
    <h1 class="od-title">
      Order <span>#{{ $order->id }}</span>
    </h1>
    <div class="od-status-pill {{ strtolower($order->status) }}">
      <div class="od-status-dot"></div>
      {{ ucfirst($order->status) }}
    </div>
  </div>

  {{-- Main Grid --}}
  <div class="od-grid">

    {{-- LEFT: Order Items --}}
    <div>
      <div class="od-card">
        <div class="od-card-head">
          <div class="od-card-head-icon">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
          </div>
          <p class="od-card-title">Order Items ({{ count($order->items) }})</p>
        </div>

        <div class="od-items-list">
          @foreach($order->items as $index => $item)
          <div class="od-item-row">

            {{-- Product Image --}}
            @if(!empty($item['image']))
              <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="od-item-img">
            @else
              <div class="od-item-img-placeholder">
                <svg width="24" height="24" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <circle cx="8.5" cy="8.5" r="1.5"/>
                  <polyline points="21 15 16 10 5 21"/>
                </svg>
              </div>
            @endif

            <div class="od-item-info">
              <div class="od-item-name">{{ $item['name'] }}</div>
              <div class="od-item-meta">
                <span>
                  <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                  </svg>
                  ${{ number_format($item['price'], 2) }} each
                </span>
                <span>
                  <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                  </svg>
                  Qty: {{ $item['quantity'] }}
                </span>
                @if(!empty($item['size']))
                <span>
                  <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
                    <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
                  </svg>
                  Size: {{ $item['size'] }}
                </span>
                @endif
              </div>
            </div>

            <div class="od-item-price">
              ${{ number_format($item['price'] * $item['quantity'], 2) }}
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- RIGHT: Sidebar --}}
    <div class="od-sidebar">

      {{-- Customer Info --}}
      <div class="od-card">
        <div class="od-card-head">
          <div class="od-card-head-icon">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <p class="od-card-title">Customer</p>
        </div>
        <div class="od-customer-body">
          <div class="od-avatar">
            <span class="od-avatar-letter">{{ strtoupper(substr($order->shipping_name, 0, 1)) }}</span>
          </div>
          <div class="od-cust-name">{{ $order->shipping_name }}</div>

          <div class="od-cust-field">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.0 2.18 2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14z"/>
            </svg>
            <div>
              <span class="od-cust-label">Phone</span>
              <span class="od-cust-val">{{ $order->shipping_phone }}</span>
            </div>
          </div>

          <div class="od-cust-field">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <div>
              <span class="od-cust-label">City</span>
              <span class="od-cust-val">{{ $order->shipping_city }}</span>
            </div>
          </div>

          <div class="od-cust-field">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <div>
              <span class="od-cust-label">Address</span>
              <span class="od-cust-val">{{ $order->shipping_address }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Order Summary --}}
      <div class="od-card">
        <div class="od-card-head">
          <div class="od-card-head-icon">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
          </div>
          <p class="od-card-title">Payment Summary</p>
        </div>
        <div class="od-summary-body">
          <div class="od-summary-row">
            <span>Payment Method</span>
            <span class="od-summary-val">{{ ucfirst($order->payment_method) }}</span>
          </div>
          <div class="od-summary-row">
            <span>Items</span>
            <span class="od-summary-val">{{ count($order->items) }}</span>
          </div>
          <div class="od-summary-row">
            <span>Placed On</span>
            <span class="od-summary-val">{{ $order->created_at->format('d M Y') }}</span>
          </div>
          <div class="od-summary-row total">
            <span>Total</span>
            <span class="od-summary-val">${{ number_format($order->total, 2) }}</span>
          </div>
        </div>
      </div>



    </div>{{-- /sidebar --}}
  </div>{{-- /grid --}}

</div>



<script>
  function openCancelModal()  { document.getElementById('cancelModal').classList.add('active'); }
  function closeCancelModal() { document.getElementById('cancelModal').classList.remove('active'); }
  document.getElementById('cancelModal').addEventListener('click', function(e){
    if(e.target === this) closeCancelModal();
  });
</script>

@endsection
