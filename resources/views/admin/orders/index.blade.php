@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <h1 class="mt-4">All Orders</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Orders</li>
  </ol>

  <div class="card mb-4">
    <div class="card-header">
      <i class="fas fa-table me-1"></i>
      Orders List
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover" id="ordersTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Total</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Customer Name</th>
            <th>Phone</th>
            <th>City</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $order)
          <tr>
            <td>{{ $order->id }}</td>
            <td>
              @if($order->user)
                {{ $order->user->name ?? 'N/A' }} <br>
                <small>{{ $order->user->email ?? '' }}</small>
              @else
                <span class="badge bg-warning">Guest</span>
              @endif
            </td>
            <td>${{ number_format($order->total, 2) }}</td>
            <td>{{ ucfirst($order->payment_method) }}</td>
            <td>
              <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : 'success' }}">
                {{ ucfirst($order->status) }}
              </span>
            </td>
            <td>{{ $order->shipping_name }}</td>
            <td>{{ $order->shipping_phone }}</td>
            <td>{{ $order->shipping_city }}</td>
            <td>{{ $order->created_at->format('d M Y h:i A') }}</td>
            <td>
              <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-eye"></i> View
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection