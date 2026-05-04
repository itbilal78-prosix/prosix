@extends('layouts.dashboard')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap');

    :root {
        --bg:       #f4f4f2;
        --surface:  #ffffff;
        --border:   #e6e6e3;
        --ink:      #0d0d0d;
        --ink-2:    #555550;
        --ink-3:    #999994;
        --accent:   #0d0d0d;
        --green:    #1a7a4a;
        --green-bg: #eaf7f0;
        --amber:    #92500a;
        --amber-bg: #fef4e4;
        --red:      #9b1c1c;
        --red-bg:   #fef2f2;
        --blue:     #1e3fa8;
        --blue-bg:  #eff3ff;
        --radius:   12px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .pw {
        background: var(--bg);
        min-height: 100vh;
        padding: 36px 28px 60px;
        font-family: 'DM Sans', sans-serif;
    }

    /* ── HEADER ── */
    .pw-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }

    .pw-eyebrow {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--ink-3);
        margin-bottom: 6px;
    }

    .pw-title {
        font-size: 30px;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.5px;
        line-height: 1;
    }

    /* ── STATS ROW ── */
    .pw-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .pw-stat {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 22px;
    }

    .pw-stat-label {
        font-size: 11px;
        font-family: 'DM Mono', monospace;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--ink-3);
        margin-bottom: 8px;
    }

    .pw-stat-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.5px;
        line-height: 1;
    }

    .pw-stat-sub {
        font-size: 12px;
        color: var(--ink-3);
        margin-top: 4px;
    }

    /* ── TABLE WRAP ── */
    .pw-table-wrap {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .pw-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1.5px solid var(--border);
    }

    .pw-table-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
    }

    .pw-total-pill {
        font-family: 'DM Mono', monospace;
        font-size: 13px;
        font-weight: 500;
        background: var(--ink);
        color: #fff;
        padding: 5px 14px;
        border-radius: 99px;
    }

    /* ── TABLE ── */
    .pw-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .pw-table thead th {
        background: #fafaf8;
        color: var(--ink-3);
        font-family: 'DM Mono', monospace;
        font-size: 10.5px;
        font-weight: 500;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 12px 20px;
        text-align: left;
        border-bottom: 1.5px solid var(--border);
        white-space: nowrap;
    }

    .pw-table tbody tr {
        border-bottom: 1px solid #f3f3f0;
        transition: background .15s;
    }

    .pw-table tbody tr:last-child { border-bottom: none; }
    .pw-table tbody tr:hover { background: #fafaf8; }

    .pw-table tbody td {
        padding: 15px 20px;
        color: var(--ink);
        vertical-align: middle;
    }

    /* ── CELLS ── */
    .pw-id {
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        color: var(--ink-3);
    }

    /* Order number */
    .pw-order-num {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        font-weight: 500;
        color: #3730a3;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        padding: 4px 10px;
        border-radius: 6px;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        transition: background .15s, border-color .15s;
    }
    .pw-order-num:hover { background: #e0e7ff; border-color: #6366f1; }

    /* Amount */
    .pw-amount {
        font-family: 'DM Mono', monospace;
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
    }

    /* Currency */
    .pw-currency {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        font-weight: 500;
        background: #f3f3f0;
        color: var(--ink-2);
        padding: 3px 8px;
        border-radius: 5px;
    }

    /* Card info */
    .pw-card {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f9f9f7;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 6px 11px;
        font-size: 12.5px;
        white-space: nowrap;
    }

    .pw-card-brand {
        font-family: 'DM Mono', monospace;
        font-weight: 600;
        color: var(--ink);
        text-transform: capitalize;
        font-size: 11px;
    }

    .pw-card-dots {
        color: var(--ink-3);
        letter-spacing: 2px;
        font-size: 10px;
    }

    .pw-card-last4 {
        font-family: 'DM Mono', monospace;
        font-weight: 600;
        color: var(--ink);
        font-size: 13px;
        letter-spacing: 1px;
    }

    .pw-card-icon {
        width: 26px;
        height: 18px;
        border-radius: 3px;
        display: grid;
        place-items: center;
        font-size: 9px;
        font-weight: 700;
        font-family: 'DM Mono', monospace;
    }

    .pw-card-icon.visa    { background: #1a1f71; color: #fff; }
    .pw-card-icon.mastercard { background: linear-gradient(135deg,#eb001b,#f79e1b); color: #fff; }
    .pw-card-icon.amex    { background: #007bc1; color: #fff; }
    .pw-card-icon.default { background: #e8e8e5; color: var(--ink-2); }

    .pw-no-card {
        font-size: 12px;
        color: var(--ink-3);
        font-style: italic;
    }

    /* Method */
    .pw-method {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--ink-2);
        background: #f3f3f0;
        padding: 4px 10px;
        border-radius: 6px;
    }

    /* Status badges */
    .pw-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 99px;
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: .04em;
        white-space: nowrap;
    }

    .pw-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .pw-badge.succeeded  { background: var(--green-bg); color: var(--green); }
    .pw-badge.paid       { background: var(--green-bg); color: var(--green); }
    .pw-badge.pending    { background: var(--amber-bg); color: var(--amber); }
    .pw-badge.failed     { background: var(--red-bg);   color: var(--red); }
    .pw-badge.refunded   { background: var(--blue-bg);  color: var(--blue); }
    .pw-badge.unknown    { background: #f3f3f0;          color: var(--ink-3); }

    /* Stripe link */
    .pw-stripe-link {
        font-family: 'DM Mono', monospace;
        font-size: 11.5px;
        color: var(--ink-2);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-bottom: 1px dashed #ccc;
        max-width: 130px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color .2s, border-color .2s;
    }

    .pw-stripe-link:hover { color: var(--ink); border-color: var(--ink); }

    /* Date */
    .pw-date {
        font-family: 'DM Mono', monospace;
        font-size: 11.5px;
        color: var(--ink-3);
        white-space: nowrap;
        line-height: 1.7;
    }

    /* Action */
    .pw-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 14px;
        background: var(--ink);
        color: #fff;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s;
        white-space: nowrap;
    }

    .pw-action-btn:hover { background: #333; color: #fff; }

    /* Empty state */
    .pw-empty {
        text-align: center;
        padding: 64px 24px;
        color: var(--ink-3);
    }

    .pw-empty svg { margin-bottom: 14px; opacity: .3; }
    .pw-empty p { font-size: 14px; }

    /* Toast */
    .pw-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: var(--ink);
        color: #fff;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        z-index: 9999;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .3s, transform .3s;
        pointer-events: none;
        font-family: 'DM Mono', monospace;
    }

    .pw-toast.show { opacity: 1; transform: translateY(0); }

    @media (max-width: 900px) {
        .pw { padding: 20px 14px 48px; }
        .pw-table { font-size: 12px; }
        .pw-table thead th,
        .pw-table tbody td { padding: 12px 12px; }
    }
</style>

<div class="pw">

    {{-- Header --}}
    <div class="pw-header">
        <div>
            <div class="pw-eyebrow">Admin Panel</div>
            <div class="pw-title">Payments</div>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $totalAmount   = $payments->sum('amount');
        $paidCount     = $payments->whereIn('real_status', ['paid','succeeded'])->count();
        $pendingCount  = $payments->where('real_status','pending')->count();
        $failedCount   = $payments->whereIn('real_status',['failed','requires_payment_method'])->count();
    @endphp

    <div class="pw-stats">
        <div class="pw-stat">
            <div class="pw-stat-label">Total Revenue</div>
            <div class="pw-stat-value">${{ number_format($totalAmount,2) }}</div>
            <div class="pw-stat-sub">{{ $payments->count() }} transactions</div>
        </div>
        <div class="pw-stat">
            <div class="pw-stat-label">Successful</div>
            <div class="pw-stat-value" style="color:var(--green)">{{ $paidCount }}</div>
            <div class="pw-stat-sub">Paid & Succeeded</div>
        </div>
        <div class="pw-stat">
            <div class="pw-stat-label">Pending</div>
            <div class="pw-stat-value" style="color:var(--amber)">{{ $pendingCount }}</div>
            <div class="pw-stat-sub">Awaiting payment</div>
        </div>
        <div class="pw-stat">
            <div class="pw-stat-label">Failed</div>
            <div class="pw-stat-value" style="color:var(--red)">{{ $failedCount }}</div>
            <div class="pw-stat-sub">Failed / Declined</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="pw-table-wrap">
        <div class="pw-table-header">
            <div class="pw-table-title">All Payments</div>
            <div class="pw-total-pill">${{ number_format($totalAmount,2) }} total</div>
        </div>

        <table class="pw-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Card</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Stripe ID</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    {{-- ID --}}
                    <td class="pw-id">{{ $payment->id }}</td>

                    {{-- Order Number --}}
                    <td>
                        @if($payment->order)
                            <span
                                class="pw-order-num"
                                onclick="copyText('{{ $payment->order->order_number }}')"
                                title="Click to copy order number"
                            >
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                {{ $payment->order->order_number ?? 'N/A' }}
                            </span>
                        @else
                            <span style="color:var(--ink-3);font-size:12px;font-style:italic;">No order</span>
                        @endif
                    </td>

                    {{-- Amount --}}
                    <td class="pw-amount">${{ number_format($payment->amount, 2) }}</td>

                    {{-- Currency --}}
                    <td><span class="pw-currency">{{ strtoupper($payment->currency) }}</span></td>

                    {{-- Card Details (Stripe se fetch) --}}
                    <td>
                        @if($payment->card_last4)
                            @php
                                $brand = strtolower($payment->card_brand ?? '');
                                $iconClass = in_array($brand, ['visa','mastercard','amex']) ? $brand : 'default';
                                $iconText  = match($brand) {
                                    'visa'       => 'VISA',
                                    'mastercard' => 'MC',
                                    'amex'       => 'AMEX',
                                    default      => strtoupper(substr($brand, 0, 4)) ?: '????',
                                };
                            @endphp
                            <div class="pw-card">
                                <div class="pw-card-icon {{ $iconClass }}">{{ $iconText }}</div>
                                <span class="pw-card-dots">••••</span>
                                <span class="pw-card-last4">{{ $payment->card_last4 }}</span>
                            </div>
                        @elseif($payment->payment_method !== 'stripe')
                            <span class="pw-no-card">—</span>
                        @else
                            <span class="pw-no-card">Not available</span>
                        @endif
                    </td>

                    {{-- Method --}}
                    <td>
                        <span class="pw-method">
                            @if($payment->payment_method === 'stripe')
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            @elseif($payment->payment_method === 'cod')
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            @endif
                            {{ ucfirst($payment->payment_method) }}
                        </span>
                    </td>

                    {{-- Real Status from Stripe --}}
                    <td>
                        @php
                            $status = $payment->real_status ?? 'unknown';
                            $badgeClass = match(true) {
                                in_array($status, ['succeeded','paid'])                     => 'succeeded',
                                $status === 'pending'                                       => 'pending',
                                in_array($status, ['failed','requires_payment_method'])     => 'failed',
                                $status === 'refunded'                                      => 'refunded',
                                default                                                     => 'unknown',
                            };
                            $badgeLabel = match(true) {
                                in_array($status, ['succeeded','paid'])                     => 'Paid',
                                $status === 'pending'                                       => 'Pending',
                                in_array($status, ['failed','requires_payment_method'])     => 'Failed',
                                $status === 'refunded'                                      => 'Refunded',
                                default                                                     => ucfirst($status),
                            };
                        @endphp
                        <span class="pw-badge {{ $badgeClass }}">
                            <span class="pw-badge-dot" style="background:currentColor"></span>
                            {{ $badgeLabel }}
                        </span>
                    </td>

                    {{-- Stripe ID --}}
                    <td>
                        @if($payment->stripe_payment_intent_id)
                            <a
                                class="pw-stripe-link"
                                href="https://dashboard.stripe.com/test/payments/{{ $payment->stripe_payment_intent_id }}"
                                target="_blank"
                                title="{{ $payment->stripe_payment_intent_id }}"
                            >
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                {{ $payment->stripe_payment_intent_id }}
                            </a>
                        @else
                            <span style="color:var(--ink-3);font-size:12px;">—</span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td class="pw-date">
                        {{ $payment->transaction_date
                            ? \Carbon\Carbon::parse($payment->transaction_date)->format('d M Y')
                            : \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                        <br>
                        <span style="color:var(--ink-3);font-size:10.5px;">
                            {{ $payment->transaction_date
                                ? \Carbon\Carbon::parse($payment->transaction_date)->format('h:i A')
                                : \Carbon\Carbon::parse($payment->created_at)->format('h:i A') }}
                        </span>
                    </td>

                    {{-- Action --}}
                    <td>
                        @if($payment->order_id)
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="pw-action-btn">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                Open Order
                            </a>
                        @else
                            <span style="color:var(--ink-3);font-size:12px;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="pw-empty">
                            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            <p>No payments found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Toast --}}
<div class="pw-toast" id="pwToast"></div>

<script>
function copyText(text) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const t = document.getElementById('pwToast');
        t.textContent = '✓ Copied: ' + text;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2000);
    });
}
</script>
@endsection
