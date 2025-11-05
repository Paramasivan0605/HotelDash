@extends('main')

@section('title', 'My Orders')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">My Orders</h1>
            
            @if($orders->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Delivery Type</th>
                                    <th>Payment Type</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $counter++ }}</td>
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
                                        <span class="badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}">
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
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    <div class="row g-3">
                        @php $counter = 1; @endphp
                        @foreach($orders as $order)
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <!-- Header with S.No and Date -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-secondary fs-6">#{{ $counter++ }}</span>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    
                                    <!-- Order Details -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Delivery Type</small>
                                            <span class="badge bg-info">{{ $order->delivery_type }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Payment Type</small>
                                            <span class="badge bg-{{ $order->payment_type == 'cash' ? 'success' : 'primary' }}">
                                                {{ ucfirst($order->payment_type) }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Total Amount</small>
                                            <strong class="text-primary">RM {{ number_format($order->order_total_price, 2) }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Status</small>
                                            <span class="badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}">
                                                {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="d-grid">
                                        <a href="{{ route('orders.details', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-cart-x display-1 text-info"></i>
                    </div>
                    <h4 class="alert-heading">No orders found</h4>
                    <p class="mb-4">You haven't placed any orders yet.</p>
                    <a href="{{ route('menu') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-menu-button me-2"></i>Browse Menu
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    /* Status Badge Styles - Only status colors remain */
    .status-ordered {
        background-color: #e3f2fd !important;
        color: #1976d2 !important;
    }

    .status-preparing {
        background-color: #fff3e0 !important;
        color: #f57c00 !important;
    }

    .status-ready {
        background-color: #e8f5e8 !important;
        color: #388e3c !important;
    }

    .status-delivery {
        background-color: #e3f2fd !important;
        color: #0288d1 !important;
    }

    .status-delivered {
        background-color: #e8f5e8 !important;
        color: #2e7d32 !important;
    }

    .status-completed {
        background-color: #e8f5e8 !important;
        color: #1b5e20 !important;
    }

    .status-cancelled {
        background-color: #ffebee !important;
        color: #c62828 !important;
    }
</style>
@endsection