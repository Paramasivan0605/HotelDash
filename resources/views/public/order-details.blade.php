@extends('main')

@section('title', 'Order Details')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="mt-4">
                <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                    ← Back to Orders
                </a>
            </div>

            <div class="row mt-2">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->customerOrderDetail as $detail)
                                        @php
                                            // Get the first food location price (you might want to store location_id in order details)
                                            $foodPrice = $detail->foodMenu->foodLocations->first()->price ?? $detail->foodMenu->price ?? 0;
                                            $itemTotal = $foodPrice * $detail->quantity;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($detail->foodMenu && $detail->foodMenu->image)
                                                        <img src="{{ asset('storage/' . $detail->foodMenu->image) }}" 
                                                             alt="{{ $detail->foodMenu->name }}" 
                                                             class="img-thumbnail me-3" 
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $detail->foodMenu->name ?? 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>RM {{ number_format($foodPrice, 2) }}</td>
                                            <td>RM {{ number_format($itemTotal, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    {{-- <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Order ID:</strong> #{{ $order->id }}
                            </div>
                            <div class="mb-3">
                                <strong>Order Date:</strong> 
                                {{ $order->created_at->format('M d, Y h:i A') }}
                            </div>
                            <div class="mb-3">
                                <strong>Delivery Type:</strong>
                                <span class="badge bg-info">{{ $order->delivery_type }}</span>
                            </div>
                            @if($order->diningTable)
                            <div class="mb-3">
                                <strong>Table:</strong> {{ $order->diningTable->table_name }}
                            </div>
                            @endif
                            <div class="mb-3">
                                <strong>Payment Method:</strong>
                                <span class="badge bg-{{ $order->payment_type == 'cash' ? 'success' : 'primary' }}">
                                    {{ ucfirst($order->payment_type) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Payment Status:</strong>
                                <span class="payment-badge {{ $order->isPaid ? 'payment-paid' : 'payment-not-paid' }}">
                                    {{ $order->isPaid ? 'Paid' : 'Not Paid' }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Order Status:</strong>
                                <span class="status-badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}">
                                    {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Contact:</strong> {{ $order->customer_contact }}
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Amount:</strong>
                                <span class="h5 text-primary mb-0">RM {{ number_format($order->order_total_price, 2) }}</span>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Order Status Timeline -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Order Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="order-timeline">
                                @php
                                    $statuses = [
                                        'ordered' => 'Order Placed',
                                        'preparing' => 'Preparing',
                                        'ready_to_deliver' => 'Ready',
                                        'delivery_on_the_way' => 'On the Way',
                                        'delivered' => 'Delivered',
                                        'completed' => 'Completed'
                                    ];

                                    // Handle both string and OrderStatusEnum cases
                                    if ($order->order_status instanceof \App\Enums\OrderStatusEnum) {
                                        $currentStatus = $order->order_status->value;
                                    } else {
                                        $currentStatus = $order->order_status;
                                    }
                                    $currentStatus = strtolower($currentStatus);
                                @endphp
                                
                                @foreach($statuses as $status => $label)
                                    @php
                                        $isActive = $currentStatus === $status;
                                        $isCompleted = array_search($currentStatus, array_keys($statuses)) > array_search($status, array_keys($statuses));
                                        $isFuture = array_search($currentStatus, array_keys($statuses)) < array_search($status, array_keys($statuses));
                                    @endphp
                                    <div class="timeline-step {{ $isActive ? 'active' : ($isCompleted ? 'completed' : '') }}">
                                        <div class="timeline-icon">
                                            @if($isActive)
                                                <i class="bi bi-arrow-right-circle-fill text-primary"></i>
                                            @elseif($isCompleted)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="timeline-label">
                                            <strong>{{ $label }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

    /* Payment Badge Styles */
    .payment-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        min-width: 80px;
        border: 1px solid transparent;
    }

    .payment-paid {
        background-color: #e8f5e8;
        color: #2e7d32;
        border-color: #a5d6a7;
    }

    .payment-not-paid {
        background-color: #ffebee;
        color: #c62828;
        border-color: #ffcdd2;
    }

    /* Order Timeline Styles */
    .order-timeline {
        position: relative;
        padding-left: 30px;
    }

    .order-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-step {
        position: relative;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .timeline-icon {
        position: absolute;
        left: -30px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        z-index: 2;
    }

    .timeline-step.completed .timeline-icon {
        color: #28a745 !important;
    }

    .timeline-step.active .timeline-icon {
        color: #007bff !important;
    }

    .timeline-label {
        margin-left: 10px;
    }

    .timeline-step.completed .timeline-label {
        color: #28a745;
    }

    .timeline-step.active .timeline-label {
        color: #007bff;
        font-weight: bold;
    }
</style>
@endsection