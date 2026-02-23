@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <h1 class="mt-4">Order #{{ $order->id }}</h1>

    <ol class="breadcrumb mb-4">
      
        <li class="breadcrumb-item">
            <a href="{{ route('admin.orders.index') }}">Orders</a>
        </li>
        <li class="breadcrumb-item active">Order Details</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <strong>Order Information</strong>
        </div>

        <div class="card-body row">
            

            <div class="col-md-6">
                <p><strong>Status:</strong>
                    <span class="badge bg-warning">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>

                <p><strong>Payment:</strong>
                    {{ ucfirst($order->payment_method) }}
                </p>

                <p><strong>Total:</strong>
                    ${{ number_format($order->total, 2) }}
                </p>

                <p><strong>Placed At:</strong>
                    {{ $order->created_at->format('d M Y h:i A') }}
                </p>
            </div>

            <div class="col-md-6">
                <h5>Customer Info</h5>

                <p><strong>Name:</strong> {{ $order->shipping_name }}</p>
                <p><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
                <p><strong>City:</strong> {{ $order->shipping_city }}</p>
                <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
            </div>

        </div>
    </div>

    {{-- Order Items --}}
    <div class="card">
        <div class="card-header">
            <strong>Order Items</strong>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Size</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>${{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ $item['size'] ?? '—' }}</td>
                        <td>
                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
