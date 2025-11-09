@extends('main')

@section('title', 'My Orders')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-dark fw-bold mb-0">
                    <i class="bi bi-receipt text-danger me-2"></i>My Orders
                </h1>
                <span class="badge bg-danger bg-gradient px-4 py-2 rounded-pill fs-6 shadow-sm">
                    {{ $orders->count() }} {{ $orders->count() == 1 ? 'Order' : 'Orders' }}
                </span>
            </div>
            
            @if($orders->count() > 0)
                <!-- Desktop/Tablet Table View -->
                <div class="d-none d-md-block">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="py-3 px-4 fw-semibold">Order ID</th>
                                        <th class="py-3 px-4 fw-semibold">Date & Time</th>
                                        <th class="py-3 px-4 fw-semibold">Delivery</th>
                                        <th class="py-3 px-4 fw-semibold">Payment</th>
                                        <th class="py-3 px-4 fw-semibold">Payment Status</th>
                                        <th class="py-3 px-4 fw-semibold">Total</th>
                                        <th class="py-3 px-4 fw-semibold">Status</th>
                                        <th class="py-3 px-4 fw-semibold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach($orders as $order)
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="fw-semibold text-dark">{{ $order->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="badge bg-info px-3 py-2">
                                                {{ $order->delivery_type }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="badge bg-{{ $order->payment_type == 'cash' ? 'success' : 'primary' }} px-3 py-2">
                                                {{ ucfirst($order->payment_type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($order->isPaid)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>Paid
                                                </span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="bi bi-x-circle me-1"></i>Not Paid
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="fw-bold text-success fs-5">RM {{ number_format($order->order_total_price, 2) }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }} px-3 py-2">
                                                {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('orders.details', $order->id) }}" class="btn btn-dark btn-sm px-3 rounded-pill">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @foreach($orders as $order)
                    <div class="card border-0 shadow rounded-4 mb-3">
                        <div class="card-body p-0 bg-white">
                            <!-- Header -->
                            <div class="bg-dark text-white p-3">
                                <h5 class="mb-2 fw-bold">#{{ $order->id }}</h5>
                                <span class="badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }} px-3 py-2">
                                    {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                                </span>
                            </div>
                            
                            <!-- Body -->
                            <div class="p-3">
                                <!-- Date & Time -->
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">
                                        <i class="bi bi-calendar3 me-1"></i>Date & Time
                                    </small>
                                    <div class="fw-semibold text-dark">{{ $order->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </div>
                                
                                <!-- Delivery & Payment -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-truck me-1"></i>Delivery
                                        </small>
                                        <span class="badge bg-info w-100 py-2">
                                            {{ $order->delivery_type }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-wallet2 me-1"></i>Payment
                                        </small>
                                        <span class="badge bg-{{ $order->payment_type == 'cash' ? 'success' : 'primary' }} w-100 py-2">
                                            {{ ucfirst($order->payment_type) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Payment Status -->
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">
                                        <i class="bi bi-credit-card-2-back me-1"></i>Payment Status
                                    </small>
                                    @if($order->isPaid)
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>Paid
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i>Not Paid
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Total Amount -->
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3">
                                    <span class="text-muted fw-semibold">Total Amount</span>
                                    <span class="fw-bold text-success fs-4">RM {{ number_format($order->order_total_price, 2) }}</span>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="d-grid">
                                    <a href="{{ route('orders.details', $order->id) }}" class="btn btn-dark py-2 rounded-pill">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- No Orders Card -->
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="card-body text-center p-5 bg-white">
                                <div class="mb-4">
                                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                        <i class="bi bi-cart-x display-3 text-danger"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold text-dark mb-3">No Orders Yet</h2>
                                <p class="text-muted mb-4 fs-5">
                                    You haven't placed any orders yet.<br>
                                    Start exploring our delicious menu!
                                </p>
                                <a href="{{ route('location.menu', ['id' => session('location_id')]) }}" class="btn btn-danger btn-lg px-5 py-3 rounded-pill shadow">
                                    <i class="bi bi-menu-button-wide me-2"></i>Browse Menu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Status Badge Styles */
    .status-ordered {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
    }

    .status-preparing {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        color: white !important;
    }

    .status-ready {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
        color: white !important;
    }

    .status-delivery {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
        color: white !important;
    }

    .status-delivered {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
        color: white !important;
    }

    .status-completed {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%) !important;
        color: white !important;
    }

    .status-cancelled {
        background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%) !important;
        color: white !important;
    }
    
    /* Table Styles */
    .table-hover tbody tr {
        transition: all 0.3s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02) !important;
        transform: scale(1.01);
    }
    
    /* Card Styles */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.2) !important;
    }
</style>
@endsection