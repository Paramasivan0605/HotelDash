@extends('main')

@section('title', 'Order Details')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('orders.history') }}" class="btn btn-dark rounded-pill shadow-sm px-4 py-2">
                    <i class="bi bi-arrow-left me-2"></i> Back to Orders
                </a>
            </div>

            <!-- Order Header Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="bg-dark text-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <i class="bi bi-receipt me-2"></i>Order #{{ $order->id }}
                            </h2>
                            <p class="mb-2">
                                <i class="bi bi-calendar3 me-2"></i>{{ $order->created_at->format('M d, Y h:i A') }}
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-phone me-2"></i>{{ $order->customer_contact }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @php
                                if ($order->order_status instanceof \App\Enums\OrderStatusEnum) {
                                    $currentStatus = $order->order_status->value;
                                } else {
                                    $currentStatus = $order->order_status;
                                }
                            @endphp
                            <span class="badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }} px-4 py-3 rounded-pill fs-5 fw-semibold d-inline-block mb-2">
                                {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                            </span>
                            <br>
                            @if($order->isPaid)
                                <span class="badge bg-success px-4 py-2 rounded-pill fs-6">
                                    <i class="bi bi-check-circle me-1"></i>Paid
                                </span>
                            @else
                                <span class="badge bg-danger px-4 py-2 rounded-pill fs-6">
                                    <i class="bi bi-x-circle me-1"></i>Not Paid
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="row g-4">
                <!-- Order Items Section -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white border-0 p-4">
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-basket3 me-2"></i>Order Items
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            @foreach($order->customerOrderDetail as $detail)
                            @php
                                $foodPrice = $detail->foodMenu->foodLocations->first()->price ?? $detail->foodMenu->price ?? 0;
                                $itemTotal = $foodPrice * $detail->quantity;
                            @endphp
                            <div class="border-bottom p-4">
                                <div class="row align-items-center g-3">
                                 
                                    
                                    <!-- Food Details -->
                                    <div class="col">
                                        <h5 class="mb-1 fw-bold text-dark">{{ $detail->foodMenu->name ?? 'N/A' }}</h5>
                                        <p class="mb-0 text-muted">
                                            <span class="badge bg-secondary rounded-pill me-2">
                                                Qty: {{ $detail->quantity }}
                                            </span>
                                            <span class="fw-semibold">
                                                {{ $order->location->currency }} {{ number_format($foodPrice, 2) }}
                                            </span> each                                        </p>
                                    </div>
                                    
                                    <!-- Item Total -->
                                    <div class="col-auto text-end">
                                        <h4 class="mb-0 text-primary fw-bold">{{ $order->location->currency }} {{ number_format($itemTotal, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Total Section -->
                            <div class="p-4 bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0 fw-bold text-dark">Total Amount</h4>
                                    <h3 class="mb-0 text-success fw-bold">{{ $order->location->currency }} {{ number_format($order->order_total_price, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Progress Timeline -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-success text-white border-0 p-4">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-hourglass-split me-2"></i>Order Progress
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $statuses = [
                                    'ordered' => ['label' => 'Order Placed', 'icon' => 'bi-receipt-cutoff'],
                                    'preparing' => ['label' => 'Preparing', 'icon' => 'bi-clock-history'],
                                    'ready_to_deliver' => ['label' => 'Ready', 'icon' => 'bi-check2-circle'],
                                    'delivery_on_the_way' => ['label' => 'On the Way', 'icon' => 'bi-truck'],
                                    'delivered' => ['label' => 'Delivered', 'icon' => 'bi-house-check'],
                                    'completed' => ['label' => 'Completed', 'icon' => 'bi-check-circle-fill']
                                ];

                                $currentStatus = strtolower($currentStatus);
                                $currentIndex = array_search($currentStatus, array_keys($statuses));
                            @endphp
                            
                            <div class="timeline">
                                @foreach($statuses as $status => $info)
                                    @php
                                        $statusIndex = array_search($status, array_keys($statuses));
                                        $isActive = $currentStatus === $status;
                                        $isCompleted = $statusIndex < $currentIndex;
                                        $isFuture = $statusIndex > $currentIndex;
                                    @endphp
                                    
                                    <div class="timeline-item {{ $isActive ? 'active' : ($isCompleted ? 'completed' : 'pending') }}">
                                        <div class="timeline-marker">
                                            @if($isCompleted)
                                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                            @elseif($isActive)
                                                <i class="bi bi-arrow-right-circle-fill text-primary fs-4"></i>
                                            @else
                                                <i class="bi bi-circle text-muted fs-5"></i>
                                            @endif
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex align-items-center">
                                                <i class="bi {{ $info['icon'] }} me-2 {{ $isActive ? 'text-primary' : ($isCompleted ? 'text-success' : 'text-muted') }}"></i>
                                                <span class="fw-bold {{ $isActive ? 'text-primary' : ($isCompleted ? 'text-success' : 'text-muted') }}">
                                                    {{ $info['label'] }}
                                                </span>
                                            </div>
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
    /* Status Badge Styles with Gradients */
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

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 10px;
        bottom: 10px;
        width: 3px;
        background: linear-gradient(to bottom, #28a745 0%, #28a745 50%, #dee2e6 50%, #dee2e6 100%);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -40px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        z-index: 2;
    }

    .timeline-content {
        flex: 1;
        padding: 12px 16px;
        background: rgba(248, 249, 250, 0.5);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .timeline-item.active .timeline-content {
        background: rgba(13, 110, 253, 0.1);
        border: 2px solid rgba(13, 110, 253, 0.3);
    }

    .timeline-item.completed .timeline-content {
        background: rgba(25, 135, 84, 0.1);
        border: 2px solid rgba(25, 135, 84, 0.2);
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .timeline {
            padding-left: 35px;
        }
        
        .timeline-marker {
            left: -35px;
            width: 35px;
            height: 35px;
        }
    }
</style>
@endsection