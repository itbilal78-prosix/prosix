<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #333; background: #f4f4f4; margin: 0; padding: 20px; }
  .page-break { page-break-after: always; }
  .email-wrap { max-width: 600px; margin: 0 auto 40px; background: #fff; border: 1px solid #ddd; }
  .header { background: #000; padding: 16px; text-align: center; }
  .header-text { color: #fff; font-size: 16px; font-weight: bold; letter-spacing: 2px; }
  .content { padding: 24px; }
  .form-title { text-align: center; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 18px; }
  table.data-table { width: 100%; border-collapse: collapse; }
  table.data-table td { padding: 8px 10px; font-size: 11px; vertical-align: top; border-bottom: 1px solid #f0f0f0; }
  table.data-table td.label { background: #f9f9f9; font-weight: bold; width: 38%; }
  .footer { background: #f4f4f4; padding: 10px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; }
  .req-number { font-size: 11px; color: #888; text-align: right; margin-bottom: 8px; }
</style>
</head>
<body>

@foreach($orders as $order)

<div class="email-wrap {{ !$loop->last ? 'page-break' : '' }}">

  <div class="header">
    <div class="header-text"> PROSIX SPORTS </div>
  </div>

  <div class="content">

    <div class="req-number">
      Order #{{ $order->order_number }} |
      {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y - h:i A') }}
    </div>

    <div class="form-title">PLACE ORDER FORM</div>

    <table class="data-table">
      <tr><td class="label">Full Name</td><td>{{ $order->full_name }}</td></tr>
      <tr><td class="label">Email</td><td>{{ $order->email }}</td></tr>
      <tr><td class="label">Order Number</td><td>{{ $order->order_number }}</td></tr>
      <tr><td class="label">Order Date</td><td>{{ $order->order_date }}</td></tr>
      <tr><td class="label">Delivery Date</td><td>{{ $order->delivery_date ?? '—' }}</td></tr>
      <tr><td class="label">Sales Rep</td><td>{{ $order->sales_rep ?? '—' }}</td></tr>
      <tr><td class="label">Team Colors</td><td>{{ $order->team_colors ?? '—' }}</td></tr>
      <tr><td class="label">Notes</td><td>{{ $order->notes ?? '—' }}</td></tr>
      <tr><td class="label">Status</td><td>{{ ucfirst($order->status) }}</td></tr>
    </table>

    {{-- MOCKUP FILES --}}
    @if(!empty($order->mockup_files))
    <div style="margin-top:16px;">
      <p style="font-weight:bold;font-size:11px;border-bottom:1px solid #eee;padding-bottom:4px;margin-bottom:10px;">
        Mockup Files
      </p>

      <table width="100%" cellpadding="4">
        <tr>
        @foreach($order->mockup_files as $file)
            @php $path = public_path('uploads/orders/mockup/'.$file); @endphp
            @if(file_exists($path))
            <td style="width:25%">
                <img src="{{ $path }}"
                     style="width:100%;max-width:120px;height:100px;object-fit:cover;border:1px solid #ddd;">
            </td>
            @endif
        @endforeach
        </tr>
      </table>
    </div>
    @endif

    {{-- ROSTER FILES --}}
    @if(!empty($order->roster_files))
    <div style="margin-top:16px;">
      <p style="font-weight:bold;font-size:11px;border-bottom:1px solid #eee;padding-bottom:4px;margin-bottom:10px;">
        Roster Files
      </p>

      <ul>
        @foreach($order->roster_files as $file)
            <li>{{ $file }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    {{-- QUOTE FILES --}}
    @if(!empty($order->quote_files))
    <div style="margin-top:16px;">
      <p style="font-weight:bold;font-size:11px;border-bottom:1px solid #eee;padding-bottom:4px;margin-bottom:10px;">
        Quote Files
      </p>

      <ul>
        @foreach($order->quote_files as $file)
            <li>{{ $file }}</li>
        @endforeach
      </ul>
    </div>
    @endif

  </div>

  <div class="footer">
    Copyright © 2009 – 2024, All Rights Reserved Design by: Prosix Sports LLC
  </div>

</div>

@endforeach

</body>
</html>
