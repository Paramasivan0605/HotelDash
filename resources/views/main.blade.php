<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
    /* Custom Styles */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        --cart-primary: #2dd4bf;
        --cart-secondary: #0ea5e9;
        --cart-gradient: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%);
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-image: url('{{ asset('images/bg-image.jpeg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }

    /* Top Navigation Bar */
    .topbar {
        background:rgb(216 23 23 / 95%) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .topbar.scrolled {
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        background: rgba(220, 38, 38, 1) !important;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-section img {
        height: 90px;
        width: auto;
    }

    .logo-section span {
        font-size: 1.5rem;
        font-weight: 700;
        color: white !important;
    }

    /* Navbar Links */
    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .navbar-nav .nav-link:hover {
        color: white !important;
        background: rgba(255, 255, 255, 0.1);
    }

    .navbar-nav .nav-link.active {
        color: white !important;
        background: rgba(255, 255, 255, 0.2);
    }

    .navbar-nav .nav-link.disabled {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* Mobile Menu Background */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: rgba(220, 38, 38, 0.98);
            margin-top: 1rem;
            border-radius: 12px;
            padding: 1rem;
        }
    }

    /* Cart Badge */
    .cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        font-size: 0.7rem;
        min-width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fbbf24 !important;
        color: #000;
        font-weight: 700;
    }

    /* Mobile Cart Wrapper */
    .mobile-cart-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (min-width: 992px) {
        .mobile-cart-wrapper {
            display: none !important;
        }
    }

    /* ============ MOBILE NAVIGATION FIXES ============ */
    
    /* Mobile: Show cart next to hamburger */
    @media (max-width: 991.98px) {
        /* Mobile cart icon visible */
        .mobile-cart-wrapper {
            display: flex !important;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Hide desktop cart and My Orders on mobile */
        .navbar-collapse .d-flex.align-items-center {
            display: none !important;
        }
        
        /* Ensure menu collapses properly */
        .navbar-collapse {
            background: rgba(220, 38, 38, 0.98);
        }
    }

    /* ============ MOBILE CART FIXES ============ */
    
    /* Cart Offcanvas Responsive Width */
    .offcanvas-cart {
        width: 100% !important;
    }

    @media (min-width: 576px) {
        .offcanvas-cart {
            max-width: 480px !important;
        }
    }

    /* Cart Header */
    .cart-header {
        background: var(--cart-gradient) !important;
        color: white;
        flex-shrink: 0;
    }

    /* Delivery Info Section */
    .delivery-info-section {
        flex-shrink: 0;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    /* Cart Summary */
    .cart-summary {
        flex-shrink: 0;
        background: var(--cart-gradient);
        color: white;
    }

    /* Scrollable Cart Items */
    .cart-items-container {
        flex: 1 1 auto;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        min-height: 0;
        padding: 1rem;
    }

    /* Cart Item Card - Mobile Optimized */
    .cart-item {
        background: white;
        border: 1px solid #e9ecef;
        border-left: 3px solid var(--cart-primary);
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .cart-item:hover {
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        transform: translateY(-2px);
    }

    .cart-item-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 2px solid #f0fdfa;
    }

    /* Mobile: Smaller image */
    @media (max-width: 576px) {
        .cart-item-img {
            width: 60px;
            height: 60px;
        }
    }

    /* Quantity Controls */
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
    }

    .qty-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        background: var(--cart-primary);
        color: white;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .qty-btn:hover {
        background: var(--cart-secondary);
        transform: scale(1.05);
    }

    @media (max-width: 576px) {
        .qty-btn {
            width: 28px;
            height: 28px;
            font-size: 0.875rem;
        }
    }

    /* Delete Button */
    .btn-delete {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Order Details Form */
    .order-details-section {
        flex-shrink: 0;
        background: #f8f9fa;
        border-top: 2px solid #dee2e6;
        max-height: 35vh;
    }

    /* Buttons */
    .gradient-button {
        background: var(--success-gradient);
        border: none;
        transition: all 0.3s ease;
    }

    .gradient-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .gradient-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-change-delivery {
        background: var(--cart-primary);
        border: none;
        color: white;
        transition: all 0.2s ease;
    }

    .btn-change-delivery:hover {
        background: var(--cart-secondary);
    }

    /* Empty Cart */
    .empty-cart {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    /* Mobile Optimizations */
    @media (max-width: 576px) {
        .logo-section {
            height: 40px;
        }
        .logo-section img{
            height: 50px;
        }
        
        .cart-item .card-body {
            padding: 0.75rem !important;
        }
        
        .cart-item h6 {
            font-size: 0.875rem;
        }
        
        .cart-item .text-muted {
            font-size: 0.75rem;
        }
        
        .order-details-section {
            padding: 0.75rem !important;
        }

        .order-details-section .form-label {
            font-size: 0.875rem;
        }

        .order-details-section .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
    }

    /* Footer */
    .footer {
        background:rgb(216 23 23 / 95%) !important;
        color: white;
        margin-top: 240px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
 
</style>
    @yield('styles')
</head>

<body data-customer-id="{{ session('customer_id') ?? '' }}">

    <!-- Top Navigation Bar - FIXED VERSION -->
    <nav class="topbar navbar navbar-expand-lg navbar-dark py-3">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand logo-section" href="#">
                <img src="{{ asset('images/logo.jpeg') }}" alt="MadrasDarbar">
            </a>

            <!-- Mobile: Cart + Hamburger Toggle -->
            <div class="mobile-cart-wrapper d-lg-none">
                <!-- Mobile Cart Icon -->
                <button class="btn btn-link text-white position-relative p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class='bx bx-cart fs-4'></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" id="cart-quantity-mobile">0</span>
                </button>
                
                <!-- Hamburger Toggle -->
                <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto p-2">
                    @if (session()->has('location_id'))
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs(['location.menu', 'search']) ? 'active fw-bold' : '' }}"
                               href="{{ route('location.menu', ['id' => session('location_id')]) }}">
                               <i class="bi bi-book"></i> Menu
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link disabled text-white-50" href="#">
                                <i class="bi bi-book"></i> Menu
                            </a>
                        </li>
                    @endif

                    @if(session('customer_id'))
                    <li class="nav-item d-lg-none">
                        <a class="nav-link text-white {{ request()->routeIs(['orders.*']) ? 'active fw-bold' : '' }}" href="{{ route('orders.history') }}">
                            <i class="bi bi-clock-history"></i> My Orders
                        </a>
                    </li>
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('customer.logout') ? 'active fw-bold' : '' }}"
                        href="{{ route('customer.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>

                <!-- Right Side Icons (Desktop Only) -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Cart (Desktop) -->
                    <button class="btn btn-link text-white position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                        <i class='bx bx-cart fs-4'></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" id="cart-quantity">0</span>
                    </button>

                    <!-- My Orders (Desktop) -->
                    @if(session('customer_id'))
                    <a href="{{ route('orders.history') }}" class="btn btn-outline-light d-none d-lg-inline-block">
                        <i class="bi bi-clock-history"></i> My Orders
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Cart Offcanvas - FIXED MOBILE VERSION -->
    <div class="offcanvas offcanvas-end offcanvas-cart" tabindex="-1" id="cartOffcanvas">
        <!-- Header -->
        <div class="offcanvas-header cart-header">
            <h5 class="offcanvas-title fw-bold mb-0">
                <i class="bi bi-cart-check"></i> Your Cart
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>

        <!-- Body with Flexbox Layout -->
        <div class="offcanvas-body p-0 d-flex flex-column">
            
            <!-- Delivery Type Info -->
            <div class="delivery-info-section p-2 d-none" id="deliveryInfoSection">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">Order Type</small>
                        <strong id="selected-delivery-type" class="text-primary"></strong>
                    </div>
                    <button type="button" class="btn btn-sm btn-change-delivery" id="changeDeliveryType">
                        <i class='bx bx-edit'></i> Change
                    </button>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary p-2">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <small class="d-block opacity-75 mb-1">Total Items</small>
                        <h5 class="mb-0 fw-bold" id="cart-item-count">0 items</h5>
                    </div>
                    <div class="col-6">
                        <small class="d-block opacity-75 mb-1">Total Amount</small>
                        <h4 class="mb-0 fw-bold" id="cart-total-amount">RM 0.00</h4>
                    </div>
                </div>
            </div>

            <!-- Cart Items - SCROLLABLE -->
            <div class="cart-items-container">
                <ul class="cart-list list-unstyled mb-0">
                    <li class="empty-cart">
                        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">No items in cart</p>
                    </li>
                </ul>
            </div>

            <!-- Order Details Form - FIXED AT BOTTOM -->
            <div class="order-details-section p-3">
                <!-- Table Number -->
                <div class="mb-3 d-none" id="tableNumberSection">
                    <label class="form-label fw-semibold small mb-2">
                        <i class="bi bi-table"></i> Table Number <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="table_number" class="form-control" placeholder="Enter table number">
                </div>

                <!-- Contact Number -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small mb-2">
                        <i class="bi bi-telephone"></i> Contact Number <span class="text-danger">*</span>
                    </label>
                    <input type="tel" name="customer_contact" class="form-control" placeholder="0123456789" required>
                </div>
                
                <!-- Optional Contact Number -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small mb-2">
                        <i class="bi bi-telephone-plus"></i> Additional Contact Number <span class="text-muted">(Optional)</span>
                    </label>
                    <input type="tel" name="additional_contact" class="form-control" placeholder="Optional second contact number">
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle"></i> In case we need to reach you at another number
                    </small>
                </div>

                <!-- Delivery Address -->
                <div class="mb-3 d-none" id="addressSection">
                    <label class="form-label fw-semibold small mb-2">
                        <i class="bi bi-geo-alt"></i> Delivery Address <span class="text-danger">*</span>
                    </label>
                    <textarea name="customer_address" class="form-control" rows="3" placeholder="Enter your full delivery address" required></textarea>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle"></i> This address will be saved to your profile
                    </small>
                </div>

                <!-- Cash Note -->
                <div class="alert alert-warning py-2 mb-3 d-none" id="cashNote">
                    <small><i class="bi bi-cash-coin"></i> <strong>Note:</strong> Cash payment only</small>
                </div>

                <!-- Confirm Button -->
                <div>
                    <small class="text-muted d-block text-center mb-2">
                        Please review your order before confirming
                    </small>
                    <button type="button" class="btn btn-success btn-lg w-100 gradient-button confirm-order" disabled>
                        <i class="bi bi-check-circle"></i> Confirm Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Delivery Type Modal - Enhanced UI -->
    <div class="modal fade" id="changeDeliveryModal" tabindex="-1" aria-labelledby="changeDeliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="w-100 text-center py-3">
                        <div class="mb-2">
                            <i class="bi bi-arrow-left-right-circle text-white" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="changeDeliveryModalLabel">
                            Change Order Type
                        </h5>
                        <p class="text-white-50 small mb-0">Select how you'd like to receive your order</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!-- Body -->
                <div class="modal-body px-4 py-4">
                    <div class="row g-3">
                        <!-- Doorstep Delivery Option -->
                        <div class="col-12">
                            <button type="button" class="btn btn-delivery-option delivery-card w-100 text-start p-0 border-0" data-option="Doorstep Delivery">
                                <div class="card border-2 h-100 shadow-sm hover-lift" style="border-color: #10b981 !important; transition: all 0.3s ease;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="delivery-icon me-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-house-door-fill text-white" style="font-size: 1.8rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 fw-bold text-dark">Doorstep Delivery</h5>
                                                <p class="mb-0 small text-muted">We'll deliver right to your door</p>
                                            </div>
                                            <div class="delivery-check ms-2" style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid #10b981; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-check text-success fw-bold" style="font-size: 1.2rem; display: none;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Counter Pickup Option -->
                        <div class="col-12">
                            <button type="button" class="btn btn-delivery-option delivery-card w-100 text-start p-0 border-0" data-option="Counter Pickup">
                                <div class="card border-2 h-100 shadow-sm hover-lift" style="border-color: #f59e0b !important; transition: all 0.3s ease;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="delivery-icon me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-shop text-white" style="font-size: 1.8rem;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 fw-bold text-dark">Counter Pickup</h5>
                                                <p class="mb-0 small text-muted">Pick up from our counter when ready</p>
                                            </div>
                                            <div class="delivery-check ms-2" style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid #f59e0b; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-check text-warning fw-bold" style="font-size: 1.2rem; display: none;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Info Alert -->
                    <div class="alert alert-info border-0 mt-4 mb-0" style="background: linear-gradient(135deg, #e0f2fe 0%, #bfdbfe 100%);">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill text-info me-2 mt-1" style="font-size: 1.2rem;"></i>
                            <small class="text-dark">
                                <strong>Note:</strong> Changing your order type will update all items in your cart and may affect delivery requirements.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    <div class="modal fade" id="paymentConfirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-credit-card"></i> Payment Method</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5 mb-4">How would you like to pay?</p>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <input class="btn-check" type="radio" name="paymentType" id="cashPayment" value="cash" checked>
                            <label class="btn btn-outline-success w-100 py-4" for="cashPayment">
                                <i class="bi bi-cash fs-1 d-block mb-2"></i>
                                <strong>Cash</strong>
                            </label>
                        </div>
                        <div class="col-6">
                            <input class="btn-check" type="radio" name="paymentType" id="cardPayment" value="card">
                            <label class="btn btn-outline-primary w-100 py-4" for="cardPayment">
                                <i class="bi bi-credit-card fs-1 d-block mb-2"></i>
                                <strong>Card</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Order Summary</h6>
                            <p id="paymentOrderSummary" class="mb-0 fw-bold">Total: RM 0.00</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPayment">
                        <i class="bi bi-check-circle"></i> Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Confirmation Modal -->
    <div class="modal fade" id="finalConfirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-cart-check-fill fs-1 text-warning d-block mb-3"></i>
                    <p id="finalConfirmationMessage" class="fs-6 text-start" style="white-space: pre-line;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="finalConfirmOrder">
                        <i class="bi bi-check-circle"></i> Yes, Confirm Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle-fill"></i> Success!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <p id="successMessage" class="fs-5 mt-3 fw-semibold"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="closeSuccessModal" data-bs-dismiss="modal">
                        <i class="bi bi-check"></i> OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                     <a class="navbar-brand logo-section" href="#">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="MadrasDarbar">
                    </a>
                </div>
                <div class="col-md-4 ">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Ask Question</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-4"><i class='bx bxl-whatsapp'></i></a>
                        <a href="#" class="text-light fs-4"><i class='bx bxl-facebook-circle'></i></a>
                        <a href="#" class="text-light fs-4"><i class='bx bxl-twitter'></i></a>
                        <a href="#" class="text-light fs-4"><i class='bx bxl-instagram-alt'></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light my-4">
            <p class="text-center text-light mb-0">© 2025 Hash Restaurant. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/public.js') }}"></script>

</body>
</html