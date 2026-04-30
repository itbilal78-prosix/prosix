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
  .file-icon { display: inline-block; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 6px 12px; font-size: 11px; font-weight: 800; color: #555; }
  .file-icon.pdf  { background: #fff0f0; color: #d32f2f; }
  .file-icon.xls  { background: #f0fff4; color: #2e7d32; }
  .file-icon.doc  { background: #e8f0fe; color: #1565c0; }
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
      <tr><td class="label">Phone</td><td>{{ $order->phone ?? '—' }}</td></tr>
      <tr><td class="label">Order Number</td><td>{{ $order->order_number }}</td></tr>
      <tr><td class="label">Order Date</td><td>{{ $order->order_date }}</td></tr>
      <tr><td class="label">Delivery Date</td><td>{{ $order->delivery_date ?? '—' }}</td></tr>
      <tr><td class="label">Sales Rep</td><td>{{ $order->sales_rep ?? '—' }}</td></tr>
      <tr><td class="label">Team Colors</td><td>{{ $order->team_colors ?? '—' }}</td></tr>
      <tr><td class="label">Notes</td><td>{!! $order->notes ?? '—' !!}</td></tr>
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
        @foreach($order->mockup_files as $index => $file)

          @if($index % 3 == 0 && $index != 0) </tr><tr> @endif

          @php
            // ✅ FIX: object ya string dono support
            $filename = is_array($file) ? ($file['filename'] ?? '') : $file;
            $original = is_array($file) ? ($file['original'] ?? $filename) : $file;
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $path     = public_path('uploads/orders/mockup/' . $filename);
            $isImage  = in_array($ext, ['jpg','jpeg','png','gif','webp']);
          @endphp

          <td width="33%" style="text-align:center;vertical-align:top;padding:6px;">
            @if($isImage && file_exists($path))
              <img src="{{ $path }}"
                   style="width:100%;max-width:120px;height:90px;object-fit:cover;border:1px solid #ddd;border-radius:4px;">
            @else
              <div class="file-icon {{ in_array($ext, ['pdf','xls','xlsx','doc','docx']) ? $ext : '' }}"
                   style="width:100%;max-width:120px;height:90px;display:flex;align-items:center;justify-content:center;margin:auto;border-radius:4px;">
                {{ strtoupper($ext) }}
              </div>
            @endif
            <div style="font-size:9px;color:#666;margin-top:4px;word-break:break-all;">{{ $original }}</div>
          </td>

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
      <ul style="margin:0;padding-left:18px;">
        @foreach($order->roster_files as $file)
          @php
            $filename = is_array($file) ? ($file['filename'] ?? '') : $file;
            $original = is_array($file) ? ($file['original'] ?? $filename) : $file;
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
          @endphp
          <li style="font-size:11px;margin-bottom:4px;">
            <span class="file-icon {{ in_array($ext,['pdf','xls','xlsx','doc','docx']) ? $ext : '' }}" style="padding:2px 6px;font-size:10px;">
              {{ strtoupper($ext) }}
            </span>
            {{ $original }}
          </li>
        @endforeach
      </ul>
    </div>
    @endif

    {{-- QUOTE FILES --}}
    @if(!empty($order->quote_files))
    <div style="margin-top:16px;">
      <p style="font-weight:bold;font-size:11px;border-bottom:1px solid #eee;padding-bottom:4px;margin-bottom:10px;">
        Quote / Invoice Files
      </p>
      <ul style="margin:0;padding-left:18px;">
        @foreach($order->quote_files as $file)
          @php
            $filename = is_array($file) ? ($file['filename'] ?? '') : $file;
            $original = is_array($file) ? ($file['original'] ?? $filename) : $file;
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
          @endphp
          <li style="font-size:11px;margin-bottom:4px;">
            <span class="file-icon {{ in_array($ext,['pdf','xls','xlsx','doc','docx']) ? $ext : '' }}" style="padding:2px 6px;font-size:10px;">
              {{ strtoupper($ext) }}
            </span>
            {{ $original }}
          </li>
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
