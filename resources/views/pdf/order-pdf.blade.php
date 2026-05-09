<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#222; background:#fff; }

  .page { padding:24px; page-break-after:always; }
  .page:last-child { page-break-after:auto; }

  /* Header */
  .header { background:#000; padding:12px 18px; margin-bottom:12px; }
  .header-inner { display:table; width:100%; }
  .header-brand { display:table-cell; color:#fff; font-size:16px; font-weight:700; letter-spacing:3px; vertical-align:middle; }
  .header-meta { display:table-cell; text-align:right; vertical-align:middle; }
  .header-meta div { color:#aaa; font-size:10px; line-height:1.6; }
  .header-meta span { color:#fff; font-weight:700; }

  /* Order meta */
  .order-meta { font-size:10px; color:#888; margin-bottom:10px; }
  .order-meta span { color:#000; font-weight:700; }

  /* Category Title */
  .cat-title { font-size:13px; font-weight:700; color:#111; border-bottom:2px solid #000; padding-bottom:5px; margin-bottom:10px; letter-spacing:1px; text-transform:uppercase; }

  /* 2-column grid */
  .items-grid { display:table; width:100%; border-collapse:separate; border-spacing:8px; }
  .items-row { display:table-row; }
  .item-cell { display:table-cell; width:50%; vertical-align:top; padding:4px; }

  /* Item block */
  .item-block { border:1px solid #e0e0e0; border-radius:5px; overflow:hidden; display:table; width:100%; }
  .item-img-cell { display:table-cell; width:110px; vertical-align:top; padding:8px; background:#f8f8f8; border-right:1px solid #e0e0e0; }
  .item-img-cell img { width:100px; height:100px; object-fit:cover; border-radius:4px; display:block; }
  .item-img-placeholder { width:100px; height:100px; background:#eee; text-align:center; line-height:100px; color:#aaa; font-size:10px; border-radius:4px; }
  .item-details-cell { display:table-cell; vertical-align:top; padding:10px 8px; }
  .item-name { font-size:12px; font-weight:700; color:#000; margin-bottom:7px; }
  .item-detail-row { display:table; width:100%; margin-bottom:4px; }
  .detail-label { display:table-cell; width:40px; font-size:9px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.4px; }
  .detail-val { display:table-cell; font-size:11px; color:#111; font-weight:600; }

  /* Footer boxes */
  .footer-row { display:table; width:100%; margin-top:14px; }
  .footer-spacer { display:table-cell; width:10px; }
  .footer-box { display:table-cell; width:49%; border:1.5px dashed #bbb; border-radius:5px; padding:10px 12px; vertical-align:top; }
  .footer-box-title { font-size:10px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px; border-bottom:1px solid #eee; padding-bottom:4px; }
  .footer-box-val { font-size:11px; color:#333; line-height:1.8; }
</style>
</head>
<body>

@foreach($orders as $order)
@php
  $groupedItems = collect($order->items)->groupBy(function($item) use ($products) {
      $product = $products->get($item['id']);
      return $product?->category?->name ?? 'Uncategorized';
  });

  // Collect all notes
  $allNotes = collect($order->items)
    ->filter(fn($item) => !empty($item['notes']))
    ->map(fn($item) => $item['name'] . ': ' . $item['notes'])
    ->implode("\n");
@endphp

<div class="page">

  {{-- Header --}}
  <div class="header">
    <div class="header-inner">
      <div class="header-brand">PROSIX SPORTS</div>
      <div class="header-meta">
        <div>Order: <span>{{ $order->order_number }}</span></div>
        <div>Date: <span>{{ $order->created_at->format('d M Y') }}</span></div>
        <div>Customer: <span>{{ $order->shipping_name }}</span></div>
      </div>
    </div>
  </div>

  <div class="order-meta">
    Order <span>{{ $order->order_number }}</span> &nbsp;·&nbsp;
    {{ $order->created_at->format('d M Y, h:i A') }} &nbsp;·&nbsp;
    Status: <span>{{ ucfirst($order->status) }}</span>
  </div>

  {{-- Items grouped by category --}}
  @foreach($groupedItems as $catName => $items)

    <div class="cat-title">Category : {{ $catName }}</div>

    @php
      $chunks = $items->values()->chunk(2);
    @endphp

    <table style="width:100%; border-collapse:separate; border-spacing:0 8px;">
      @foreach($chunks as $pair)
      <tr>
        @foreach($pair as $item)
        @php
          $imgUrl = !empty($item['image']) ? $item['image'] : null;
          if ($imgUrl && str_contains($imgUrl, '/storage/')) {
              $imgPath = public_path('storage/' . \Illuminate\Support\Str::after($imgUrl, '/storage/'));
              $imgUrl = file_exists($imgPath) ? $imgPath : null;
          } elseif ($imgUrl && str_contains($imgUrl, '127.0.0.1')) {
              // localhost URL se path nikalo
              $parsed = parse_url($imgUrl);
              $imgPath = public_path($parsed['path']);
              $imgUrl = file_exists($imgPath) ? $imgPath : null;
          }
        @endphp
        <td style="width:50%; padding:4px; vertical-align:top;">
          <table style="width:100%; border:1px solid #e0e0e0; border-radius:5px; overflow:hidden;">
            <tr>
              <td style="width:110px; padding:8px; background:#f8f8f8; border-right:1px solid #e0e0e0; vertical-align:top;">
                @if($imgUrl)
                  <img src="{{ $imgUrl }}" style="width:100px;height:100px;object-fit:cover;border-radius:4px;display:block;">
                @else
                  <div style="width:100px;height:100px;background:#eee;text-align:center;line-height:100px;color:#aaa;font-size:10px;border-radius:4px;">No Image</div>
                @endif
              </td>
              <td style="padding:10px 8px; vertical-align:top;">
                <div style="font-size:12px;font-weight:700;color:#000;margin-bottom:7px;">{{ $item['name'] }}</div>
                <table style="width:100%;">
                  <tr>
                    <td style="font-size:9px;font-weight:700;color:#888;text-transform:uppercase;width:40px;">Size</td>
                    <td style="font-size:11px;color:#111;font-weight:600;">{{ $item['size'] ?? '—' }}</td>
                  </tr>
                  <tr>
                    <td style="font-size:9px;font-weight:700;color:#888;text-transform:uppercase;">Qty</td>
                    <td style="font-size:11px;color:#111;font-weight:600;">{{ $item['quantity'] }}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
        @endforeach
        {{-- Agar sirf 1 item hai toh empty cell --}}
        @if($pair->count() === 1)
        <td style="width:50%; padding:4px;"></td>
        @endif
      </tr>
      @endforeach
    </table>

  @endforeach

  {{-- Footer: Address + Notes --}}
  <table style="width:100%; margin-top:16px; border-collapse:separate; border-spacing:10px 0;">
    <tr>
      <td style="width:50%; border:1.5px dashed #bbb; border-radius:5px; padding:10px 12px; vertical-align:top;">
        <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;border-bottom:1px solid #eee;padding-bottom:4px;">Address</div>
        <div style="font-size:11px;color:#333;line-height:1.8;">
          {{ $order->shipping_name }}<br>
          {{ $order->shipping_address }}<br>
          {{ $order->shipping_city }}@if($order->shipping_province), {{ $order->shipping_province }}@endif<br>
          {{ $order->shipping_phone }}<br>
          @if($order->shipping_email){{ $order->shipping_email }}@endif
        </div>
      </td>
      <td style="width:50%; border:1.5px dashed #bbb; border-radius:5px; padding:10px 12px; vertical-align:top;">
        <div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;border-bottom:1px solid #eee;padding-bottom:4px;">Notes</div>
        <div style="font-size:11px;color:#333;line-height:1.8;white-space:pre-line;">{{ $allNotes ?: ($order->admin_notes ?? '—') }}</div>
      </td>
    </tr>
  </table>

</div>
@endforeach

</body>
</html>
