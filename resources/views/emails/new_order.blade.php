<!DOCTYPE html>
<html>
<head>
    <title>New Order</title>
</head>
<body>
    <h1>New Order Placed</h1>
    <p>Order ID: {{ $order->id }}</p>
    <p>Total: PKR {{ $order->total }}</p>
    <p>Payment: {{ $order->payment_method }}</p>
    <p>Customer: {{ $order->shipping_name }} ({{ $order->shipping_phone }})</p>
    <p>Address: {{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
    <p>Delivery: {{ $order->delivery_days }}</p>
    <h2>Items:</h2>
    <ul>
        @foreach(json_decode($order->items) as $item)
            <li>{{ $item->name }} (Size: {{ $item->size }}, Qty: {{ $item->quantity }}, Price: {{ $item->price }})</li>
        @endforeach
    </ul>
</body>
</html>