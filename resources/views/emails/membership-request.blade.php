<!DOCTYPE html>
<html>
<head>
    <title>New Membership Request</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f4f4f4;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            padding: 0;
        }

        /* ===== Top Bar ===== */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            border-bottom: 2px solid #000;
        }

        .header img {
            height: 45px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }

        .content {
            padding: 20px;
        }

        h2 {
            margin-top: 0;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f5f5f5;
            width: 35%;
        }

        /* ===== User Image ===== */
        .user-image {
            text-align: center;
            margin-top: 25px;
        }

        .user-image img {
            max-width: 180px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #777;
            text-align: center;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- ===== Header Bar ===== -->
    <div class="header">
        <!-- Left Logo -->
        <img src="{{ asset('/public/assets/images/P LOGO BLACK.png') }}" alt="Prosix Logo">

        <!-- Right Company Name -->
        <div class="company-name">Prosix Sports</div>
    </div>

    <div class="content">
        <h2>New Membership / Special Deal Request</h2>
        <p>A new user has submitted a membership request:</p>

        <!-- ===== Data Table ===== -->
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $data->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $data->email }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $data->phone }}</td>
            </tr>
            <tr>
                <th>Organization / School</th>
                <td>{{ $data->organization }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>{{ $data->role }}</td>
            </tr>
            <tr>
                <th>Sports (max 2)</th>
                <td>{{ implode(', ', $data->sports) }}</td>
            </tr>
            <tr>
                <th>Apparel Level</th>
                <td>{{ $data->level }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $data->address }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $data->state ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <th>ZIP / Postal Code</th>
                <td>{{ $data->zip ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <th>Submitted On</th>
                <td>{{ $data->created_at->format('d M Y - h:i A') }}</td>
            </tr>
        </table>

        <!-- ===== Requester Image ===== -->
        @if(!empty($data->image))
        <div class="user-image">
            <p><strong>Submitted Image</strong></p>
            <img src="{{ asset('storage/' . $data->image) }}" alt="User Image">
        </div>
        @endif

        <p style="margin-top: 30px;">
            <strong>Action:</strong> Login to admin panel → Memberships section to view all requests.
        </p>
    </div>

    <div class="footer">
        Prosix Sports – Team
    </div>

</div>

</body>
</html>
