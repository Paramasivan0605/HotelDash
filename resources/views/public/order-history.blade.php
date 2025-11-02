@extends('main')

@section('title', 'My Orders')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">My Orders</h1>
            
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Delivery Type</th>
                                <th>Payment Type</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $order->delivery_type }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->payment_type == 'cash' ? 'success' : 'primary' }}">
                                        {{ ucfirst($order->payment_type) }}
                                    </span>
                                </td>
                                <td>RM {{ number_format($order->order_total_price, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->order_status == 'completed' ? 'success' : 'warning' }}">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.details', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <h4>No orders found</h4>
                    <p>You haven't placed any orders yet.</p>
                    <a href="{{ route('menu') }}" class="btn btn-primary">Browse Menu</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection