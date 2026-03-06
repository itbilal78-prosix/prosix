<!DOCTYPE html>
<html>
<head>
    <title>New Membership Request</title>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:30px;background:#f4f4f4;font-family:Arial,sans-serif;">

<div style="max-width:650px;margin:0 auto;background:#ffffff;border-radius:0;overflow:hidden;border:1px solid #ddd;">

    <!-- ===== HEADER ===== -->
    <div style="background:#000000;padding:18px 24px;text-align:center;border-bottom:3px solid #222;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align:center;vertical-align:middle;">
                    <img src="{{ $message->embedData(file_get_contents(public_path('assets/images/P LOGO WHITE.png')), 'p-logo.png', 'image/png') }}"
                         alt="P" style="height:42px;vertical-align:middle;">
                    <span style="display:inline-block;width:1px;height:38px;background:rgba(255,255,255,0.4);vertical-align:middle;margin:0 16px;"></span>
                    <img src="{{ $message->embedData(file_get_contents(public_path('assets/images/PROSIX SPORTS LOGO PNG WHITE.png')), 'prosix-logo.png', 'image/png') }}"
                         alt="Prosix Sports" style="height:36px;vertical-align:middle;">
                </td>
            </tr>
        </table>
    </div>

    <!-- ===== CONTENT ===== -->
    <div style="padding:28px 30px;">

        <h2 style="text-align:center;font-size:18px;font-weight:800;color:#000;letter-spacing:1px;text-transform:uppercase;margin:0 0 24px;border-bottom:2px solid #000;padding-bottom:14px;">
            MEMBERSHIP REQUEST FORM
        </h2>

        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;width:38%;vertical-align:top;">Full Name</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->name }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Email</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->email }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Phone</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->phone }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Organization / School</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->organization }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Role</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->role }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Sports (max 2)</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ implode(', ', $data->sports) }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Apparel Level</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->level }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Address</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->address }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">State</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->state ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">ZIP / Postal Code</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->zip ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;vertical-align:top;">Submitted On</td>
                <td style="padding:10px 12px;font-size:13px;vertical-align:top;">{{ $data->created_at->format('d M Y - h:i A') }}</td>
            </tr>
        </table>

        <!-- User Image -->
        @if(!empty($data->image))
        <div style="margin-top:24px;text-align:center;">
            <p style="font-weight:bold;font-size:13px;margin-bottom:10px;">Submitted Image</p>
            <img src="{{ $message->embed(storage_path('app/public/'.$data->image)) }}"
                 style="max-width:180px;border-radius:8px;border:1px solid #ddd;">
        </div>
        @endif

    </div>

    <!-- ===== FOOTER ===== -->
    <div style="background:#f4f4f4;padding:14px;text-align:center;font-size:12px;color:#888;border-top:1px solid #ddd;">
        Copyright &copy; 2009 – 2024, All Rights Reserved Design by: Prosix Sports LLC
    </div>

</div>
</body>
</html>
