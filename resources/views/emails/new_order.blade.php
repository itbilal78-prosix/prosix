<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <meta charset="utf-8">
</head>

<body style="margin:0;padding:30px;background:#f4f4f4;font-family:Arial,sans-serif;">

<div style="max-width:650px;margin:0 auto;background:#ffffff;border:1px solid #ddd;">

    <!-- HEADER -->
    <div style="background:#000;padding:18px;text-align:center;border-bottom:3px solid #222;">
        <img src="{{ url('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
             alt="Prosix Sports"
             style="height:36px;">
    </div>


    <!-- CONTENT -->
    <div style="padding:30px;">

        <h2 style="text-align:center;font-size:18px;font-weight:800;color:#000;letter-spacing:1px;text-transform:uppercase;border-bottom:2px solid #000;padding-bottom:10px;">
            ORDER CONFIRMATION
        </h2>


        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-top:20px;">

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;width:40%;">Order Number</td>
                <td style="padding:10px;">#{{ $order->order_number }}</td>
            </tr>

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">Total Amount</td>
                <td style="padding:10px;">${{ $order->total }}</td>
            </tr>

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">Payment Method</td>
                <td style="padding:10px;">{{ ucfirst($order->payment_method) }}</td>
            </tr>

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">Customer</td>
                <td style="padding:10px;">
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_phone }}
                </td>
            </tr>

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">Shipping Address</td>
                <td style="padding:10px;">
                    {{ $order->shipping_address }},
                    {{ $order->shipping_city }}
                </td>
            </tr>

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">Delivery Time</td>
                <td style="padding:10px;">
                    {{ $order->delivery_days }}
                </td>
            </tr>

        </table>


        <!-- ITEMS -->
        <h3 style="margin-top:30px;border-bottom:1px solid #ddd;padding-bottom:8px;">
            Order Items
        </h3>

        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">

            @foreach ($order->items as $item)

            <tr style="border-bottom:1px solid #eee;">
                <td>{{ $item['name'] }}</td>

                <td>Size: {{ $item['size'] ?? '-' }}</td>

                <td>Qty: {{ $item['quantity'] }}</td>

                <td>${{ $item['price'] }}</td>
            </tr>

            @endforeach

        </table>


        <p style="margin-top:25px;font-size:14px;">
            Thank you for shopping with <strong>Prosix Sports</strong>.
        </p>

    </div>


    <!-- FOOTER -->
    <div style="background:#f4f4f4;padding:14px;text-align:center;font-size:12px;color:#888;border-top:1px solid #ddd;">
        © {{ date('Y') }} Prosix Sports LLC — All Rights Reserved
    </div>

</div>

</body>
</html>
