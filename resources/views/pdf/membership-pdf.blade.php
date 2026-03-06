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
@foreach($requests as $m)
<div class="email-wrap {{ !$loop->last ? 'page-break' : '' }}">

  <div class="header">
    <div class="header-text"> PROSIX SPORTS </div>
  </div>

  <div class="content">
    <div class="req-number">Request #{{ $m->id }} &nbsp;|&nbsp; {{ $m->created_at->format('d M Y - h:i A') }}</div>
    <div class="form-title">MEMBERSHIP REQUEST FORM</div>

    <table class="data-table">
      <tr><td class="label">Full Name</td><td>{{ $m->name }}</td></tr>
      <tr><td class="label">Email</td><td>{{ $m->email }}</td></tr>
      <tr><td class="label">Phone</td><td>{{ $m->phone }}</td></tr>
      <tr><td class="label">Organization / School</td><td>{{ $m->organization }}</td></tr>
      <tr><td class="label">Role</td><td>{{ $m->role }}</td></tr>
      <tr><td class="label">Sports (max 2)</td><td>{{ implode(', ', $m->sports ?? []) }}</td></tr>
      <tr><td class="label">Apparel Level</td><td>{{ $m->level }}</td></tr>
      <tr><td class="label">Address</td><td>{{ $m->address }}</td></tr>
      <tr><td class="label">State</td><td>{{ $m->state ?? 'Not provided' }}</td></tr>
      <tr><td class="label">ZIP / Postal Code</td><td>{{ $m->zip ?? 'Not provided' }}</td></tr>
      <tr><td class="label">Submitted On</td><td>{{ $m->created_at->format('d M Y - h:i A') }}</td></tr>
    </table>
  </div>

  <div class="footer">Copyright &copy; 2009 – 2024, All Rights Reserved Design by: Prosix Sports LLC</div>
</div>
@endforeach
</body>
</html>
