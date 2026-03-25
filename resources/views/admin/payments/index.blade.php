@extends('layouts.dashboard')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');

    .pay-wrap {
        padding: 28px 24px;
        font-family: 'DM Sans', sans-serif;
        background: #f8f8f8;
        min-height: 100vh;
    }

    .pay-title {
        font-size: 26px;
        font-weight: 700;
        color: #0a0a0a;
        margin-bottom: 20px;
        letter-spacing: -.3px;
    }

    .pay-table-wrap {
        background: #fff;
        border: 1.5px solid #e8e8e8;
        border-radius: 14px;
        overflow: hidden;
    }

    .pay-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 18px;
    }

    .pay-table thead tr {
        background: #0a0a0a;
    }

    .pay-table thead th {
        color: #fff;
        font-family: 'DM Mono', monospace;
        font-size: 17px;
        font-weight: 500;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 16px 20px;
        border: none;
        white-space: nowrap;
    }

    .pay-table tbody tr {
        border-bottom: 1px solid #f2f2f2;
        transition: background .15s;
    }

    .pay-table tbody tr:last-child {
        border-bottom: none;
    }

    .pay-table tbody tr:hover {
        background: #fafafa;
    }

    .pay-table tbody td {
        padding: 16px 20px;
        color: #333;
        border: none;
        vertical-align: middle;
        font-size: 18px;
    }

    .pay-id {
        font-family: 'DM Mono', monospace;
        font-size: 18px;
        color: #aaa;
    }

    .pay-order {
        font-weight: 600;
        color: #0a0a0a;
        font-family: 'DM Mono', monospace;
        font-size: 18px;
    }

    .pay-amount {
        font-family: 'DM Mono', monospace;
        font-weight: 600;
        color: #0a0a0a;
        font-size: 18px;
    }

    .pay-currency {
        font-family: 'DM Mono', monospace;
        font-size: 17px;
        color: #888;
        background: #f5f5f5;
        padding: 4px 10px;
        border-radius: 5px;
    }

    .pay-method {
        color: #555;
        font-size: 18px;
    }

    .pay-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 18px;
        font-weight: 600;
        font-family: 'DM Mono', monospace;
        letter-spacing: .04em;
    }

    .pay-badge.paid {
        background: #0a0a0a;
        color: #fff;
    }

    .pay-badge.pending {
        background: #f5f5f5;
        color: #888;
        border: 1.5px solid #e0e0e0;
    }

    .pay-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .pay-stripe {
        font-family: 'DM Mono', monospace;
        font-size: 20px;
        color: #0a0a0a;
        text-decoration: none;
        max-width: 160px;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
        border-bottom: 1px dashed #ccc;
    }

    .pay-stripe:hover {
        color: #000;
        border-color: #000;
    }

    .pay-date {
        font-family: 'DM Mono', monospace;
        font-size: 17px;
        color: #888;
        white-space: nowrap;
    }

    .pay-open-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #0a0a0a;
        color: #fff;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        transition: background .2s;
        white-space: nowrap;
    }

    .pay-open-btn:hover {
        background: #333;
        color: #fff;
    }
</style>

<div class="pay-wrap">

    <div class="pay-title">Payments Overview</div>

    <div class="pay-table-wrap">
        <table class="pay-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Stripe ID</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td class="pay-id">{{ $payment->id }}</td>

                    <td class="pay-order">
                        {{ optional($payment->order)->order_number ?? 'N/A' }}
                    </td>

                    <td class="pay-amount">${{ $payment->amount }}</td>

                    <td>
                        <span class="pay-currency">{{ strtoupper($payment->currency) }}</span>
                    </td>

                    <td class="pay-method">{{ ucfirst($payment->payment_method) }}</td>

                    <td>
                        @if($payment->payment_status == 'paid')
                            <span class="pay-badge paid">
                                <span class="pay-badge-dot"></span> Paid
                            </span>
                        @else
                            <span class="pay-badge pending">
                                <span class="pay-badge-dot"></span> Pending
                            </span>
                        @endif
                    </td>

                    <td>
                        <a target="_blank" class="pay-stripe"
                           href="https://dashboard.stripe.com/test/payments/{{ $payment->stripe_payment_intent_id }}"
                           title="{{ $payment->stripe_payment_intent_id }}">
                            {{ $payment->stripe_payment_intent_id }}
                        </a>
                    </td>

                    <td class="pay-date">{{ $payment->transaction_date }}</td>

                    <td>
                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="pay-open-btn">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                                <polyline points="15 3 21 3 21 9"/>
                                <line x1="10" y1="14" x2="21" y2="3"/>
                            </svg>
                            Open Order
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
