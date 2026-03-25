<!DOCTYPE html>
<html>

<head>
    <title>Order Confirmation</title>
</head>

<body>

<h1>Order Placed Successfully 🎉</h1>

<p><strong>Order Number:</strong> {{ $order->order_number }}</p>

<p><strong>Total:</strong> ${{ $order->total }}</p>

<p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>

<p>
<strong>Customer:</strong>
{{ $order->shipping_name }}
({{ $order->shipping_phone }})
</p>

<p>
<strong>Address:</strong>
{{ $order->shipping_address }},
{{ $order->shipping_city }}
</p>

<p>
<strong>Delivery Time:</strong>
{{ $order->delivery_days }}
</p>


<h2>Items:</h2>

<ul>

@foreach ($order->items as $item)

<li>

{{ $item['name'] }}

(Size: {{ $item['size'] ?? '-' }},

Qty: {{ $item['quantity'] }},

Price: ${{ $item['price'] }})

</li>

@endforeach

</ul>


<br>

<p>Thank you for shopping with Prosix Sports.</p>

</body>

</html>
