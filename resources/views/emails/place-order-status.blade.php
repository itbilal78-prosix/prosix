<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Order Status Update</title>
</head>

<body style="margin:0;padding:30px;background:#f4f4f4;font-family:Arial,sans-serif;">

<div style="max-width:650px;margin:0 auto;background:#ffffff;border:1px solid #ddd;">

    <!-- HEADER -->
    <div style="background:#000;padding:18px;text-align:center;">
        <img src="{{ url('assets/images/P LOGO WHITE.png') }}" style="height:40px;">
        <span style="display:inline-block;width:1px;height:30px;background:#555;margin:0 10px;"></span>
        <img src="{{ url('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}" style="height:32px;">
    </div>

    <!-- CONTENT -->
    <div style="padding:30px;">

        <h2 style="text-align:center;font-weight:800;text-transform:uppercase;border-bottom:2px solid #000;padding-bottom:10px;margin-bottom:25px;">
            ORDER STATUS UPDATE
        </h2>

        <p style="font-size:15px;">Hello <b>{{ $order->full_name }}</b>,</p>
        <p style="font-size:15px;">Your order status has been updated.</p>

        <!-- ORDER INFO -->
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:20px;">
            <tr>
                <td style="padding:10px;background:#f9f9f9;font-weight:bold;width:40%;">Order #</td>
                <td style="padding:10px;font-weight:bold;">{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td style="padding:10px;background:#f9f9f9;font-weight:bold;">Order Date</td>
                <td style="padding:10px;">{{ $order->order_date }}</td>
            </tr>
            <tr>
                <td style="padding:10px;background:#f9f9f9;font-weight:bold;">Delivery Date</td>
                <td style="padding:10px;">{{ $order->delivery_date ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding:10px;background:#f9f9f9;font-weight:bold;">New Status</td>
                <td style="padding:10px;">
                    @php
                        $colors = [
                            'pending'    => '#f59e0b',
                            'processing' => '#3b82f6',
                            'completed'  => '#22c55e',
                            'cancelled'  => '#ef4444',
                        ];
                        $color = $colors[$order->status] ?? '#888';
                    @endphp
                    <span style="background:{{ $color }};color:#fff;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:700;">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- STATUS MESSAGE -->
        <div style="margin-top:25px;padding:15px;border-radius:8px;
            @if($order->status === 'completed') background:#f0fdf4;border:1px solid #bbf7d0;
            @elseif($order->status === 'processing') background:#eff6ff;border:1px solid #bfdbfe;
            @elseif($order->status === 'cancelled') background:#fef2f2;border:1px solid #fecaca;
            @else background:#fffbeb;border:1px solid #fde68a; @endif">

            @if($order->status === 'completed')
                <p style="margin:0;font-size:14px;color:#166534;">🎉 Your order has been completed! Thank you for choosing Prosix Sports.</p>
            @elseif($order->status === 'processing')
                <p style="margin:0;font-size:14px;color:#1e40af;">⚙️ Your order is being processed. We will notify you when it is ready.</p>
            @elseif($order->status === 'cancelled')
                <p style="margin:0;font-size:14px;color:#991b1b;">❌ Your order has been cancelled. Please contact us if you have any questions.</p>
            @else
                <p style="margin:0;font-size:14px;color:#92400e;">⏳ Your order is pending. We will update you soon.</p>
            @endif
        </div>

        <p style="margin-top:25px;font-size:14px;">
            Thank you for choosing <b>Prosix Sports</b>!
        </p>

    </div>

    <!-- FOOTER -->
    <div style="background:#f4f4f4;padding:12px;text-align:center;font-size:12px;color:#888;">
        © {{ date('Y') }} Prosix Sports LLC — All Rights Reserved
    </div>

</div>

</body>
</html>
