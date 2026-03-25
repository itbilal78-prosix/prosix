<!DOCTYPE html>
<html>
<head>
    <title>Order Update</title>
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
            ORDER UPDATE
        </h2>


        <p style="font-size:14px;">
            Hello <strong>{{ $order->shipping_name }}</strong>,
        </p>

        <p style="font-size:14px;">
            Your order <strong>#{{ $order->order_number }}</strong> has been updated.
        </p>


        <table width="100%" cellpadding="0" cellspacing="0"
               style="border-collapse:collapse;margin-top:20px;">

            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;width:40%;">
                    Current Status
                </td>
                <td style="padding:10px;">
                    {{ ucfirst($order->status) }}
                </td>
            </tr>


            @if($order->courier_name)
            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">
                    Courier
                </td>
                <td style="padding:10px;">
                    {{ $order->courier_name }}
                </td>
            </tr>
            @endif


            @if($order->tracking_number)
            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">
                    Tracking Number
                </td>
                <td style="padding:10px;">
                    {{ $order->tracking_number }}
                </td>
            </tr>
            @endif


            @if($order->admin_notes)
            <tr>
                <td style="background:#f9f9f9;padding:10px;font-weight:bold;">
                    Admin Note
                </td>
                <td style="padding:10px;">
                    {{ $order->admin_notes }}
                </td>
            </tr>
            @endif

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
