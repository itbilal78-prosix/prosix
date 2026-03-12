<!DOCTYPE html>
<html>
<head>
    <title>New Artwork Request</title>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:30px;background:#f4f4f4;font-family:Arial,sans-serif;">

<div style="max-width:650px;margin:0 auto;background:#ffffff;border-radius:0;overflow:hidden;border:1px solid #ddd;">

    <!-- HEADER -->
    <div style="background:#000000;padding:18px 24px;text-align:center;border-bottom:3px solid #222;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align:center;vertical-align:middle;">
                    <img src="{{ url('assets/images/P LOGO WHITE.png') }}"
                         alt="P" style="height:42px;vertical-align:middle;">
                    <span style="display:inline-block;width:1px;height:38px;background:rgba(255,255,255,0.4);vertical-align:middle;margin:0 16px;"></span>
                    <img src="{{ url('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
                         alt="Prosix Sports" style="height:36px;vertical-align:middle;">
                </td>
            </tr>
        </table>
    </div>

    <!-- CONTENT -->
    <div style="padding:28px 30px;">
        <h2 style="text-align:center;font-size:18px;font-weight:800;color:#000;letter-spacing:1px;text-transform:uppercase;margin:0 0 24px;border-bottom:2px solid #000;padding-bottom:14px;">
            ARTWORK REQUEST FORM
        </h2>

        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;width:38%;">Full Name</td><td style="padding:10px 12px;font-size:13px;">{{ $data->full_name }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Email</td><td style="padding:10px 12px;font-size:13px;">{{ $data->email }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Phone</td><td style="padding:10px 12px;font-size:13px;">{{ $data->phone }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Instagram</td><td style="padding:10px 12px;font-size:13px;">{{ $data->instagram }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Address</td><td style="padding:10px 12px;font-size:13px;">{{ $data->address }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Team / Organization</td><td style="padding:10px 12px;font-size:13px;">{{ $data->team_name }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Role</td><td style="padding:10px 12px;font-size:13px;">{{ $data->role }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Order Quantity</td><td style="padding:10px 12px;font-size:13px;">{{ $data->quantity }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Team Color</td><td style="padding:10px 12px;font-size:13px;">{{ $data->team_color }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Home/Away Mockup</td><td style="padding:10px 12px;font-size:13px;">{{ $data->home_away }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Design Style</td><td style="padding:10px 12px;font-size:13px;">{{ $data->design_style }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Material</td><td style="padding:10px 12px;font-size:13px;">{{ $data->material }}</td></tr>
            <tr>
                <td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Sport(s)</td>
                <td style="padding:10px 12px;font-size:13px;">
                    @if(is_array($data->products))
                        {{ implode(', ', $data->products) }}
                    @else
                        {{ $data->products }}
                    @endif
                </td>
            </tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">Additional Details</td><td style="padding:10px 12px;font-size:13px;">{{ $data->additional }}</td></tr>
            <tr><td style="padding:10px 12px;background:#f9f9f9;font-weight:bold;font-size:13px;">How Did You Hear About Us</td><td style="padding:10px 12px;font-size:13px;">{{ $data->source }}</td></tr>
        </table>

        @if(!empty($data->artwork_file))
        @php $images = is_string($data->artwork_file) ? json_decode($data->artwork_file) : $data->artwork_file; @endphp
        @if(!empty($images))
        <div style="margin-top:24px;">
            <p style="font-weight:bold;font-size:13px;margin-bottom:10px;border-bottom:1px solid #eee;padding-bottom:6px;">Uploaded Reference Images</p>
            <table cellpadding="4" cellspacing="0"><tr>
            @foreach($images as $img)
            <td><img src="{{ url('uploads/artwork/'.$img) }}" style="width:140px;height:140px;object-fit:cover;border-radius:6px;border:1px solid #ddd;"></td>
            @endforeach
            </tr></table>
        </div>
        @endif
        @endif
    </div>

    <!-- FOOTER -->
    <div style="background:#f4f4f4;padding:14px;text-align:center;font-size:12px;color:#888;border-top:1px solid #ddd;">
        Copyright &copy; 2009 – 2024, All Rights Reserved Design by: Prosix Sports LLC
    </div>
</div>
</body>
</html>
