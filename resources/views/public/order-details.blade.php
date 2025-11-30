<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700;800;900&family=Cinzel:wght@400;500;600;700;800;900&family=Great+Vibes&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Tailwind CSS v3.4+ CDN (with JIT, dark mode, and all plugins) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Optional: Tailwind Config (Recommended for better performance & custom colors) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#dc2626',
                            600: '#b91c1c',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js v3 (Required for modals & reactivity) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Optional: Google Fonts - Inter (Modern & clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>     
<body>
     <div class="top-bar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-telephone-fill me-2"></i>
                    <span>{{ session('customer_phone') }} | {{ strtoupper(session('order_type', 'delivery')) }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    <span>{{ $location->location_name }}</span>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar sticky-top">
        <div class="container">
            <div class="d-flex align-items-center w-100 justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="navbar-brand me-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="MadrasDarbar" style="height: 40px;">
                    </div>
                    <div>
                        <p class="restaurant-name mb-0">MadrasDarbar - {{ $location->location_name }}</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-none d-md-inline-flex" type="button" onclick="toggleSearch()">
                        <i class="bi bi-search me-2"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </nav>
<div class="container-fluid px-3 py-3">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('orders.history') }}" class="btn btn-outline-dark bg-danger btn-sm text-white" style="">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Order Header -->
    <div class="order-header mb-4">
        <h1 class="h5 fw-bold text-dark mb-2">Order #{{ $order->order_code }}</h1>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-dark d-block">{{ $order->created_at->format('M d, Y • h:i A') }}</small>
                <small class="text-dark">{{ $order->customer_contact }}</small>
            </div>
            <div class="text-end">
                <span class="badge {{ $order->isPaid ? 'bg-success' : 'bg-danger' }} text-white small">
                    {{ $order->isPaid ? 'Paid' : 'Not Paid' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Order Status -->
    <div class="status-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">Order Status</h6>
            <span class="status-badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}">
                {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
            </span>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress mb-3" style="height: 6px;">
            @php
                // Get the string value from the Enum
                $currentStatus = $order->order_status instanceof \App\Enums\OrderStatusEnum 
                    ? $order->order_status->value 
                    : (string) $order->order_status;
                
                $statuses = ['ordered', 'preparing', 'ready_to_deliver', 'delivery_on_the_way', 'delivered', 'completed'];
                $currentIndex = array_search($currentStatus, $statuses);
                $progress = $currentIndex !== false ? (($currentIndex + 1) / count($statuses) * 100) : 0;
            @endphp
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%"></div>
        </div>

        <!-- Status Steps -->
        <div class="status-steps">
            @php
                $steps = [
                    'ordered' => ['icon' => 'bi-cart-check', 'label' => 'Ordered'],
                    'preparing' => ['icon' => 'bi-clock', 'label' => 'Preparing'],
                    'ready_to_deliver' => ['icon' => 'bi-check-circle', 'label' => 'Ready'],
                    'delivery_on_the_way' => ['icon' => 'bi-truck', 'label' => 'On the Way'],
                    'delivered' => ['icon' => 'bi-house-check', 'label' => 'Delivered'],
                    'completed' => ['icon' => 'bi-check-lg', 'label' => 'Completed']
                ];
            @endphp
            
            <div class="d-flex justify-content-between">
                @foreach($steps as $status => $step)
                    @php
                        $isActive = $currentStatus === $status;
                        $stepIndex = array_search($status, array_keys($steps));
                        $isCompleted = $stepIndex <= $currentIndex;
                    @endphp
                    <div class="step text-center">
                        <div class="step-icon {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                            <i class="bi {{ $step['icon'] }}"></i>
                        </div>
                        <small class="step-label {{ $isCompleted ? 'text-success fw-bold' : 'text-muted' }}">
                            {{ $step['label'] }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="order-items-card">
        <h6 class="fw-bold text-dark mb-3">Order Items</h6>
        
        @foreach($order->customerOrderDetail as $detail)
        @php
            $foodPrice = $detail->foodMenu->foodLocations->first()->price ?? $detail->foodMenu->price ?? 0;
            $itemTotal = $foodPrice * $detail->quantity;
        @endphp
        <div class="order-item mb-3 pb-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">{{ $detail->foodMenu->name ?? 'N/A' }}</h6>
                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-3">Qty: {{ $detail->quantity }}</span>
                        <span class="text-muted small">{{ $order->location->currency }} {{ number_format($foodPrice, 2) }} each</span>
                    </div>
                </div>
                <div class="text-end">
                    <span class="fw-bold text-dark">{{ $order->location->currency }} {{ number_format($itemTotal, 2) }}</span>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Order Summary -->
        <div class="order-summary pt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="text-dark">{{ $order->location->currency }} {{ number_format($order->order_total_price, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Delivery</span>
                <span class="text-dark">Free</span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="fw-bold text-dark">Total</span>
                <span class="fw-bold text-dark fs-5">{{ $order->location->currency }} {{ number_format($order->order_total_price, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Delivery Info -->
    <div class="delivery-info mt-4">
        <h6 class="fw-bold text-dark mb-3">Delivery Information</h6>
        <div class="info-grid">
            <div class="info-item">
                <small class="text-muted d-block">Delivery Type</small>
                <span class="fw-medium text-dark">{{ $order->delivery_type }}</span>
            </div>
            <div class="info-item">
                <small class="text-muted d-block">Payment Method</small>
                <span class="fw-medium text-dark text-capitalize">{{ $order->payment_type }}</span>
            </div>
            @if($order->customer_address)
            <div class="info-item">
                <small class="text-muted d-block">Delivery Address</small>
                <span class="fw-medium text-dark">{{ $order->customer_address }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Base Styles */
    body {
        background-image: url('{{ asset('images/bg-image.jpeg') }}');
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .top-bar {
        background-color: #8B0000;
        color: white;
        padding: 10px 0;
        font-size: 14px;
    }

    .navbar {
        background-color: rgb(216 25 25);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 15px 0;
        color: white;
    }

    .restaurant-name {
        font-size: 16px;
        font-weight: 600;
        color: white;
        margin-bottom: 0;
    }

    /* Order Header */
    .order-header {
        padding: 0;
    }

    /* Status Card */
    .status-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    /* Status Badges */
    .status-badge {
        padding: 6px 12px;
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

    /* Progress Steps */
    .status-steps {
        margin-top: 20px;
    }

    .step {
        flex: 1;
        position: relative;
    }

    .step-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .step-icon.completed {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }

    .step-icon.active {
        background: #007bff;
        border-color: #007bff;
        color: white;
    }

    .step-label {
        font-size: 0.7rem;
        line-height: 1.2;
    }

    /* Order Items */
    .order-items-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .order-item:last-child {
        border-bottom: none !important;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    /* Order Summary */
    .order-summary {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        margin-top: 16px;
    }

    /* Delivery Info */
    .delivery-info {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .info-grid {
        display: grid;
        gap: 16px;
    }

    .info-item {
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-outline-dark {
        border: 1px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-dark:hover {
        background: #6c757d;
        color: white;
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
            padding-left: 16px;
            padding-right: 16px;
        }
        
        .status-card,
        .order-items-card,
        .delivery-info {
            padding: 16px;
            border-radius: 10px;
        }
        
        .step-label {
            font-size: 0.65rem;
        }
        
        .step-icon {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
        }
    }

    /* Smooth transitions */
    .status-card,
    .order-items-card,
    .delivery-info,
    .btn {
        transition: all 0.2s ease;
    }
</style>
</body>
</html>