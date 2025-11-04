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
                                    <span class="status-badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}">
                                        {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
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

<style>
    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        min-width: 120px;
        border: 1px solid transparent;
    }

    /* Status Colors */
    .status-ordered {
        background-color: #e3f2fd;
        color: #1976d2;
        border-color: #bbdefb;
    }

    .status-preparing {
        background-color: #fff3e0;
        color: #f57c00;
        border-color: #ffe0b2;
    }

    .status-ready {
        background-color: #e8f5e8;
        color: #388e3c;
        border-color: #c8e6c9;
    }

    .status-delivery {
        background-color: #e3f2fd;
        color: #0288d1;
        border-color: #b3e5fc;
    }

    .status-delivered {
        background-color: #e8f5e8;
        color: #2e7d32;
        border-color: #a5d6a7;
    }

    .status-completed {
        background-color: #e8f5e8;
        color: #1b5e20;
        border-color: #81c784;
    }

    .status-cancelled {
        background-color: #ffebee;
        color: #c62828;
        border-color: #ffcdd2;
    }
</style>
@endsection