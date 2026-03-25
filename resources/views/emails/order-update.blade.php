<!DOCTYPE html>
<html>
<head>
    <title>Order Update</title>
</head>
<body>

<h2>Hello {{ $order->shipping_name }}</h2>

<p>Your order <strong>#{{ $order->id }}</strong> has been updated.</p>

<p>
<strong>Status:</strong> {{ ucfirst($order->status) }}
</p>

@if($order->courier_name)
<p>
<strong>Courier:</strong> {{ $order->courier_name }}
</p>
@endif

@if($order->tracking_number)
<p>
<strong>Tracking Number:</strong> {{ $order->tracking_number }}
</p>
@endif

@if($order->admin_notes)
<p>
<strong>Admin Note:</strong> {{ $order->admin_notes }}
</p>
@endif

<br>

<p>Thank you for shopping with Prosix Sports.</p>

</body>
</html>
