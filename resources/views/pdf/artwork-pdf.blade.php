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
@foreach($requests as $i => $req)
<div class="email-wrap {{ !$loop->last ? 'page-break' : '' }}">

  <div class="header">
    <div class="header-text"> PROSIX SPORTSssss </div>
  </div>

  <div class="content">
    <div class="req-number">Request #{{ $req->id }} &nbsp;|&nbsp; {{ $req->created_at->format('d M Y - h:i A') }}</div>
    <div class="form-title">ARTWORK REQUEST FORM</div>

    <table class="data-table">
      <tr><td class="label">Full Name</td><td>{{ $req->full_name }}</td></tr>
      <tr><td class="label">Email</td><td>{{ $req->email }}</td></tr>
      <tr><td class="label">Phone</td><td>{{ $req->phone ?? '—' }}</td></tr>
      <tr><td class="label">Instagram</td><td>{{ $req->instagram ?? '—' }}</td></tr>
      <tr><td class="label">Address</td><td>{{ $req->address ?? '—' }}</td></tr>
      <tr><td class="label">Team / Organization</td><td>{{ $req->team_name ?? '—' }}</td></tr>
      <tr><td class="label">Role</td><td>{{ $req->role ?? '—' }}</td></tr>
      <tr><td class="label">Order Quantity</td><td>{{ $req->quantity ?? '—' }}</td></tr>
      <tr><td class="label">Team Color</td><td>{{ $req->team_color ?? '—' }}</td></tr>
      <tr><td class="label">Home / Away Mock-up</td><td>{{ $req->home_away ?? '—' }}</td></tr>
      <tr><td class="label">Design Style</td><td>{{ $req->design_style ?? '—' }}</td></tr>
      <tr><td class="label">Material</td><td>{{ $req->material ?? '—' }}</td></tr>
      <tr>
        <td class="label">Sport(s)</td>
        <td>{{ is_array($req->products) ? implode(', ', $req->products) : $req->products }}</td>
      </tr>
      <tr><td class="label">Mockup Request Details</td><td>{{ $req->additional ?? '—' }}</td></tr>
      <tr><td class="label">How Heard About Us</td><td>{{ $req->source ?? '—' }}</td></tr>
    </table>

    {{-- ── Uploaded Images ── --}}
    @php
      $imgs = $req->artwork_file ? json_decode($req->artwork_file) : [];
    @endphp
    @if(!empty($imgs))
    <div style="margin-top:16px;">
      <p style="font-weight:bold;font-size:11px;border-bottom:1px solid #eee;padding-bottom:4px;margin-bottom:10px;">Uploaded Reference Images</p>
      <table width="100%" cellpadding="4" cellspacing="0">
        <tr>
          @foreach($imgs as $img)
            @php $imgPath = public_path('uploads/artwork/' . $img); @endphp
            @if(file_exists($imgPath))
            <td style="width:25%;vertical-align:top;">
              <img src="{{ $imgPath }}"
                   style="width:100%;max-width:120px;height:100px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">
            </td>
            @endif
          @endforeach
        </tr>
      </table>
    </div>
    @endif

  </div>

  <div class="footer">Copyright &copy; 2009 – 2024, All Rights Reserved Design by: Prosix Sports LLC</div>
</div>
@endforeach
</body>
</html>
