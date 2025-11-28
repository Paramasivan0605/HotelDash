@extends('main')

@section('title', 'My Orders')

@section('content')
<div class="container-fluid px-3 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">My Orders</h1>
            <p class=" small mb-0" style="color:red;">{{ $orders->count() }} {{ $orders->count() == 1 ? 'order' : 'orders' }}</p>
        </div>
        <a href="{{ route('location.menu', ['id' => session('location_id')]) }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-plus"></i> New Order
        </a>
    </div>

    @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card mb-3">
                <!-- Order Header -->
                <div class="order-header d-flex justify-content-between align-items-center p-3 border-bottom">
                    <div>
                        <div class="fw-bold text-dark">Order #{{ $order->order_code }}</div>
                        <small class="text-muted">{{ $order->created_at->format('M d, Y • h:i A') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-dark">{{ $order->location->currency }} {{ number_format($order->order_total_price, 2) }}</div>
                        <span class="badge {{ $order->isPaid ? 'bg-success' : 'bg-danger' }} text-white small">
                            {{ $order->isPaid ? 'Paid' : 'Not Paid' }}
                        </span>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="order-body p-3">
                    <!-- Status -->
                    <div class="status-section mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Status</span>
                            <span class="status-badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}">
                                {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Delivery & Payment -->
                    <div class="details-section mb-3">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted small mb-1">Delivery Type</div>
                                <div class="fw-medium text-dark">{{ $order->delivery_type }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small mb-1">Payment</div>
                                <div class="fw-medium text-dark text-capitalize">{{ $order->payment_type }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="action-section">
                        <a href="{{ route('orders.details', $order->id) }}" class="btn btn-outline-dark w-100 py-2">
                            <i class="bi bi-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state text-center py-5">
            <div class="empty-icon mb-4">
                <i class="bi bi-bag-x display-1 text-muted"></i>
            </div>
            <h3 class="h5 fw-bold text-dark mb-2">No orders yet</h3>
            <p class="text-muted mb-4">You haven't placed any orders yet</p>
            <a href="{{ route('location.menu', ['id' => session('location_id')]) }}" class="btn btn-danger px-4">
                Browse Menu
            </a>
        </div>
    @endif
</div>

<style>
    /* Base Styles */
    body {
        background-image: url('{{ asset('images/bg-image.jpeg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Order Card */
    .order-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .order-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    /* Order Header */
    .order-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    /* Status Badges - Simplified */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-ordered { background: #e3f2fd; color: #1976d2; }
    .status-preparing { background: #fff3e0; color: #f57c00; }
    .status-ready { background: #e8f5e8; color: #388e3c; }
    .status-delivery { background: #fff8e1; color: #ffa000; }
    .status-delivered { background: #f3e5f5; color: #7b1fa2; }
    .status-completed { background: #e8f5e8; color: #388e3c; }
    .status-cancelled { background: #ffebee; color: #d32f2f; }

    /* Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-danger {
        background: #dc2626;
        border: none;
    }

    .btn-danger:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }

    .btn-outline-dark {
        border: 1px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-dark:hover {
        background: #6c757d;
        color: white;
    }

    .btn-outline-danger {
        border: 1px solid #dc2626;
        color: #dc2626;
    }

    .btn-outline-danger:hover {
        background: #dc2626;
        color: white;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 1rem;
    }

    .empty-icon {
        opacity: 0.5;
    }

    /* Text Colors */
    .text-dark {
        color: #1f2937 !important;
    }

    .text-muted {
        color: #6b7280 !important;
    }

    /* Badges */
    .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 12px;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 12px;
            padding-right: 12px;
        }
        
        .order-card {
            border-radius: 10px;
        }
        
        .order-header,
        .order-body {
            padding: 16px;
        }
    }

    /* Smooth transitions */
    .order-card,
    .btn,
    .badge {
        transition: all 0.2s ease;
    }

    /* Focus states */
    .btn:focus {
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25);
    }
</style>
@endsection