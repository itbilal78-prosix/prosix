<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Order Confirmation</title>
</head>

<body style="margin:0;padding:30px;background:#f5f5f5;font-family:Arial,sans-serif;">

<div style="max-width:900px;margin:auto;background:#fff;border:1px solid #ddd;padding:30px;">

    <!-- HEADER -->
    <div style="text-align:center;margin-bottom:30px;">
        <h2 style="margin:0;font-weight:900;letter-spacing:2px;">
            THANKS FOR CHOOSING US!
        </h2>
        <p style="font-size:12px;color:#777;letter-spacing:2px;margin-top:5px;">
            WE REALLY APPRECIATE & VALUE YOUR BUSINESS
        </p>
    </div>

    <!-- TOP FIELDS -->
    <table width="100%" cellspacing="10">
        <tr>
            <td>
                <label>Full Name</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                    {{ $order->full_name }}
                </div>
            </td>

            <td>
                <label>Email</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                    {{ $order->email }}
                </div>
            </td>

            <td>
                <label>Order Date</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                    {{ $order->order_date }}
                </div>
            </td>

            <td>
                <label>Delivery Date</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                    {{ $order->delivery_date ?? '-' }}
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <label>Sales Rep</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                    {{ $order->sales_rep ?? '-' }}
                </div>
            </td>

            <td>
                <label>Team Colors</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;">
                    {{ $order->team_colors ?? '-' }}
                </div>
            </td>

            <td colspan="2">
                <label>Order #</label>
                <div style="border:1px solid #ccc;padding:10px;border-radius:6px;font-weight:bold;">
                    {{ $order->order_number }}
                </div>
            </td>
        </tr>
    </table>

    <hr style="margin:25px 0;">

    <!-- ✅ STATUS SECTION -->
    @php
        $statusColors = [
            'pending'    => '#f59e0b',
            'processing' => '#3b82f6',
            'completed'  => '#22c55e',
            'cancelled'  => '#ef4444',
        ];
        $statusColor = $statusColors[$order->status] ?? '#888';
    @endphp

    <div style="text-align:center;margin-bottom:25px;">
        <span style="background:{{ $statusColor }};color:#fff;padding:8px 24px;border-radius:20px;font-size:14px;font-weight:700;letter-spacing:1px;">
            STATUS: {{ strtoupper($order->status) }}
        </span>
    </div>

    @if($order->status === 'completed')
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin-bottom:20px;text-align:center;">
        <p style="margin:0;color:#166534;font-size:14px;font-weight:600;">
            🎉 Your order has been completed! Thank you for choosing Prosix Sports.
        </p>
    </div>
    @elseif($order->status === 'processing')
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:16px;margin-bottom:20px;">
        <p style="margin:0;color:#1e40af;font-size:14px;">
            ⚙️ Your order is currently being processed. We'll notify you when it's ready.
        </p>
    </div>
    @elseif($order->status === 'cancelled')
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;margin-bottom:20px;">
        <p style="margin:0;color:#991b1b;font-size:14px;">
            ❌ Your order has been cancelled. Please contact us if you have any questions.
        </p>
    </div>
    @endif
    <!-- ✅ STATUS SECTION END -->

    <!-- MOCKUP IMAGES -->
    @if(!empty($order->mockup_files))
    <div style="margin-top:20px;">
        <p style="font-weight:bold;">Mockup Files</p>

        <table width="100%" cellpadding="5">
            <tr>
            @foreach($order->mockup_files as $index => $file)

                @if($index % 3 == 0 && $index != 0)
                    </tr><tr>
                @endif

                @php
                    $filename = is_array($file) ? $file['filename'] : $file;
                    $original = is_array($file) ? ($file['original'] ?? $filename) : $file;
                    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $url      = url('uploads/orders/mockup/' . $filename);
                    $isImage  = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                @endphp

                <td width="33%" style="text-align:center;vertical-align:top;padding:8px;">

                    @if($isImage)
                        <a href="{{ $url }}" target="_blank">
                            <img src="{{ $url }}"
                                 style="width:100%;max-width:160px;height:110px;object-fit:cover;border:1px solid #ddd;border-radius:6px;">
                        </a>
                    @else
                        <div style="width:100%;max-width:160px;height:110px;border:1px solid #ddd;border-radius:6px;display:flex;align-items:center;justify-content:center;margin:auto;background:#f8f8f8;">
                            <span style="font-size:13px;font-weight:700;color:#555;">{{ strtoupper($ext) }}</span>
                        </div>
                    @endif

                    <div style="margin-top:6px;font-size:11px;color:#555;">{{ $original }}</div>

                    <div style="margin-top:5px;">
                        <a href="{{ $url }}"
                           style="display:inline-block;background:#000;color:#fff;font-size:11px;padding:4px 10px;border-radius:4px;text-decoration:none;">
                            Download
                        </a>
                    </div>

                </td>

            @endforeach
            </tr>
        </table>
    </div>
    @endif

    <!-- ROSTER FILES -->
    @if(!empty($order->roster_files))
    <div style="margin-top:20px;">
        <p style="font-weight:bold;">Roster Files</p>

        <table width="100%" cellpadding="5">
            <tr>
            @foreach($order->roster_files as $index => $file)

                @if($index % 3 == 0 && $index != 0)
                    </tr><tr>
                @endif

                @php
                    $filename = is_array($file) ? $file['filename'] : $file;
                    $original = is_array($file) ? ($file['original'] ?? $filename) : $file;
                    $url      = url('uploads/orders/roster/' . $filename);
                @endphp

                <td width="33%" style="text-align:center;">
                    <a href="{{ $url }}" style="font-size:12px;color:#000;text-decoration:none;">
                        📄 {{ $original }}
                    </a>
                </td>

            @endforeach
            </tr>
        </table>
    </div>
    @endif

    <!-- QUOTE FILES -->
    @if(!empty($order->quote_files))
    <div style="margin-top:20px;">
        <p style="font-weight:bold;">Quote / Invoice Files</p>

        <table width="100%" cellpadding="5">
            <tr>
            @foreach($order->quote_files as $index => $file)

                @if($index % 3 == 0 && $index != 0)
                    </tr><tr>
                @endif

                @php
                    $filename = is_array($file) ? $file['filename'] : $file;
                    $original = is_array($file) ? ($file['original'] ?? $filename) : $file;
                    $url      = url('uploads/orders/quote/' . $filename);
                @endphp

                <td width="33%" style="text-align:center;">
                    <a href="{{ $url }}" style="font-size:12px;color:#000;text-decoration:none;">
                        📄 {{ $original }}
                    </a>
                </td>

            @endforeach
            </tr>
        </table>
    </div>
    @endif

    <!-- NOTES -->
    <div style="margin-top:25px;">
        <p><strong>Notes:</strong></p>
        <div style="border:1px solid #ccc;padding:15px;border-radius:8px;min-height:60px;">
            {!! $order->notes ?? 'No notes provided' !!}
        </div>
    </div>

    <!-- FOOTER -->
    <div style="margin-top:30px;text-align:center;color:#888;font-size:12px;">
        Thank you for choosing Prosix Sports!
    </div>

</div>

</body>
</html>
