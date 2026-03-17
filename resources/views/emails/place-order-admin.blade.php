<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>New Order (Admin)</title>
</head>

<body style="margin:0;padding:30px;background:#f4f4f4;font-family:Arial,sans-serif;">

<div style="max-width:900px;margin:0 auto;background:#ffffff;border:1px solid #ddd;">

    <!-- HEADER -->
    <div style="background:#000;padding:18px;text-align:center;">
        <img src="{{ url('assets/images/P LOGO WHITE.png') }}" style="height:40px;">
        <span style="display:inline-block;width:1px;height:30px;background:#555;margin:0 10px;"></span>
        <img src="{{ url('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}" style="height:32px;">
    </div>

    <!-- CONTENT -->
    <div style="padding:30px;">

        <h2 style="text-align:center;font-weight:800;text-transform:uppercase;border-bottom:2px solid #000;padding-bottom:10px;margin-bottom:25px;">
            NEW PLACE ORDER RECEIVED
        </h2>

        <!-- TOP GRID -->
        <table width="100%" cellspacing="10">
            <tr>
                <td>
                    <label><b>Full Name</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                        {{ $order->full_name }}
                    </div>
                </td>

                <td>
                    <label><b>Email</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                        {{ $order->email }}
                    </div>
                </td>

                <td>
                    <label><b>Order Date</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                        {{ $order->order_date }}
                    </div>
                </td>

                <td>
                    <label><b>Delivery Date</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                        {{ $order->delivery_date ?? '-' }}
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <label><b>Sales Rep</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                        {{ $order->sales_rep ?? '-' }}
                    </div>
                </td>

                <td>
                    <label><b>Team Colors</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                        {{ $order->team_colors ?? '-' }}
                    </div>
                </td>

                <td colspan="2">
                    <label><b>Order #</b></label>
                    <div style="border:1px solid #ccc;padding:10px;border-radius:6px;font-weight:bold;">
                        {{ $order->order_number }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- STATUS -->
        <div style="margin-top:20px;">
            <span style="background:#000;color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;">
                STATUS: {{ strtoupper($order->status) }}
            </span>
        </div>

        <!-- NOTES -->
        <div style="margin-top:20px;">
            <b>Notes:</b>
            <div style="border:1px solid #ccc;padding:15px;border-radius:6px;margin-top:5px;">
                {!! $order->notes ?? 'No notes' !!}
            </div>
        </div>

        <hr style="margin:25px 0;">

        <!-- MOCKUP FILES -->
        @if(!empty($order->mockup_files))
        <div>
            <p style="font-weight:bold;">Final Mockup Files</p>

            <table width="100%" cellpadding="6">
                <tr>
                @foreach($order->mockup_files as $index => $file)

                    @if($index % 3 == 0 && $index != 0)
                        </tr><tr>
                    @endif

                    @php $url = url('uploads/orders/mockup/' . $file); @endphp

                    <td width="33%" style="text-align:center;">
                        <img src="{{ $url }}"
                             style="width:100%;max-width:150px;height:110px;object-fit:cover;border:1px solid #ddd;border-radius:6px;">

                        <br>
                        <a href="{{ $url }}" style="font-size:11px;text-decoration:none;color:#1565c0;">
                            ⬇ Download
                        </a>
                    </td>

                @endforeach
                </tr>
            </table>
        </div>
        @endif

        <!-- ROSTER -->
        @if(!empty($order->roster_files))
        <div style="margin-top:20px;">
            <p style="font-weight:bold;">Roster Files</p>

            <table width="100%" cellpadding="6">
                <tr>
                @foreach($order->roster_files as $index => $file)

                    @if($index % 3 == 0 && $index != 0)
                        </tr><tr>
                    @endif

                    @php $url = url('uploads/orders/roster/' . $file); @endphp

                    <td width="33%" style="text-align:center;">
                        <a href="{{ $url }}">{{ $file }}</a>
                    </td>

                @endforeach
                </tr>
            </table>
        </div>
        @endif

        <!-- QUOTE -->
        @if(!empty($order->quote_files))
        <div style="margin-top:20px;">
            <p style="font-weight:bold;">Quote / Invoice Files</p>

            <table width="100%" cellpadding="6">
                <tr>
                @foreach($order->quote_files as $index => $file)

                    @if($index % 3 == 0 && $index != 0)
                        </tr><tr>
                    @endif

                    @php $url = url('uploads/orders/quote/' . $file); @endphp

                    <td width="33%" style="text-align:center;">
                        <a href="{{ $url }}">{{ $file }}</a>
                    </td>

                @endforeach
                </tr>
            </table>
        </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div style="background:#f4f4f4;padding:12px;text-align:center;font-size:12px;color:#888;">
        Admin Notification - New Order Received
    </div>

</div>

</body>
</html>
