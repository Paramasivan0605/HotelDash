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
<style>
    /* Modern Design Variables */
    :root {
        --primary-color: #e20006;
        --primary-dark: #b80005;
        --secondary-color: #ff1a1f;
        --accent-color: #14b8a6;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --dark-bg: #1e293b;
        --light-bg: #f8fafc;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Raleway', sans-serif;
        background-image: url('{{ asset('images/bg-image.jpeg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* ============ MODERN NAVIGATION ============ */
    .topbar {
        background: rgba(226, 0, 6, 0.95) !important;
        backdrop-filter: blur(20px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 1030;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid rgba(226, 0, 6, 0.3);
    }

    .topbar.scrolled {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background: rgba(226, 0, 6, 1) !important;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 12px;
        transition: transform 0.3s ease;
    }

    .logo-section:hover {
        transform: scale(1.02);
    }

    .logo-section img {
        height: 50px;
        width: auto;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
    }

    .logo-section span {
        font-size: 1.5rem;
        font-weight: 800;
        color: white !important;
        font-family: 'Cinzel', serif;
        letter-spacing: 1px;
    }

    /* Modern Nav Links */
    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.6rem 1.2rem !important;
        border-radius: 12px;
        transition: all 0.3s ease;
        position: relative;
        font-family: 'Raleway', sans-serif;
        letter-spacing: 0.5px;
    }

    .navbar-nav .nav-link:hover {
        color: white !important;
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }

    .navbar-nav .nav-link.active {
        color: white !important;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .navbar-nav .nav-link.disabled {
        color: rgba(255, 255, 255, 0.4) !important;
        opacity: 0.5;
    }

    /* Mobile Navigation */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: rgba(226, 0, 6, 0.98);
            margin-top: 1rem;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .logo-section img {
            height: 45px;
        }

        .logo-section span {
            font-size: 1.25rem;
        }
    }

    /* Modern Cart Button */
    .cart-btn {
        position: relative;
        background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%);
        color: white !important;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        font-family: 'Raleway', sans-serif;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(20, 184, 166, 0.4);
    }

    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        min-width: 22px;
        height: 22px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fbbf24;
        color: #000;
        font-weight: 700;
        border-radius: 11px;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Mobile Cart Wrapper */
    .mobile-cart-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    @media (min-width: 992px) {
        .mobile-cart-wrapper {
            display: none !important;
        }
    }

    /* Hamburger Icon Modern Style */
    .navbar-toggler {
        border: none;
        padding: 0.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23ffffff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2.5' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* ============ CART OFFCANVAS (ORIGINAL DESIGN) ============ */
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
        background: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%) !important;
        color: white;
        flex-shrink: 0;
    }

    .cart-header .offcanvas-title {
        font-family: 'Cinzel', serif;
        font-weight: 700;
        letter-spacing: 1px;
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
        background: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%);
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
        border-left: 3px solid #2dd4bf;
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
        background: #2dd4bf;
        color: white;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .qty-btn:hover {
        background: #0ea5e9;
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
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        transition: all 0.3s ease;
        font-family: 'Raleway', sans-serif;
        font-weight: 700;
        letter-spacing: 0.5px;
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
        background: #2dd4bf;
        border: none;
        color: white;
        transition: all 0.2s ease;
        font-family: 'Raleway', sans-serif;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .btn-change-delivery:hover {
        background: #0ea5e9;
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

    /* ============ MODERN MODALS ============ */
    .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        font-family: 'Raleway', sans-serif;
    }

    .modal-header {
        border: none;
        padding: 2rem;
    }

    .modal-header .modal-title {
        font-family: 'Cinzel', serif;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        border: none;
        padding: 1.5rem 2rem;
        background: var(--light-bg);
    }

    /* Delivery Option Cards */
    .delivery-card .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .delivery-card .card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
    }

    .delivery-card.active .card {
        border-color: var(--primary-color) !important;
        border-width: 3px !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .delivery-card.active .delivery-check i {
        display: block !important;
    }

    /* ============ MODERN FOOTER ============ */
    .footer {
        background: rgba(226, 0, 6, 0.95);
        color: white;
        margin-top: 4rem;
        padding: 3rem 0 1.5rem;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        font-family: 'Raleway', sans-serif;
    }

    .footer h5 {
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: white;
        font-family: 'Cinzel', serif;
        letter-spacing: 1px;
    }

    .footer a {
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .footer a:hover {
        color: white;
        transform: translateX(4px);
        display: inline-block;
    }

    .footer .social-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .footer .social-icon:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    /* Responsive Utilities */
    @media (max-width: 576px) {
        .order-details-section {
            padding: 1rem;
        }

        .gradient-button {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }

        .cart-header {
            padding: 1.25rem;
        }

        .cart-header .offcanvas-title {
            font-size: 1.25rem;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cart-item {
        animation: fadeIn 0.3s ease forwards;
    }

    /* Scrollbar Styling */
    .cart-items-container::-webkit-scrollbar {
        width: 8px;
    }

    .cart-items-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .cart-items-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%);
        border-radius: 10px;
    }

    .cart-items-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #0284c7 0%, #14b8a6 100%);
    }

    /* My Orders Button */
    .btn-orders {
        background: linear-gradient(135deg, #e20006 0%, #ff1a1f 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(226, 0, 6, 0.3);
        font-family: 'Raleway', sans-serif;
        letter-spacing: 0.5px;
    }

    .btn-orders:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(226, 0, 6, 0.4);
        color: white;
    }
</style>
    @yield('styles')
</head>

<body data-customer-id="{{ session('customer_id') ?? '' }}">

   <!-- Modern Navigation Bar - Only show if not hiding layout -->
    @if(!isset($hideLayout) || !$hideLayout)
    <nav class="topbar navbar navbar-expand-lg navbar-light py-2">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand logo-section" href="#">
                <img src="{{ asset('images/logo.jpeg') }}" alt="MadrasDarbar">
                <span class="d-none d-sm-inline"></span>
            </a>

            <!-- Mobile: Cart + Hamburger -->
            <div class="mobile-cart-wrapper d-lg-none">
                <button class="btn cart-btn position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class='bx bx-cart fs-5'></i>
                    <span class="cart-badge" id="cart-quantity-mobile">0</span>
                </button>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    @if (session()->has('location_id'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs(['location.menu', 'search']) ? 'active' : '' }}"
                               href="{{ route('location.menu', ['id' => session('location_id')]) }}">
                               <i class="bi bi-book me-1"></i> Menu
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#">
                                <i class="bi bi-book me-1"></i> Menu
                            </a>
                        </li>
                    @endif

                    @if(session('customer_id'))
                    <li class="nav-item d-lg-none">
                        <a class="nav-link {{ request()->routeIs(['orders.*']) ? 'active' : '' }}" href="{{ route('orders.history') }}">
                            <i class="bi bi-clock-history me-1"></i> My Orders
                        </a>
                    </li>
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link"
                        href="{{ route('customer.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>

                <!-- Desktop Actions -->
                <div class="d-none d-lg-flex align-items-center gap-2">
                    <button class="btn cart-btn position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                        <i class='bx bx-cart fs-5 me-1'></i> Cart
                        <span class="cart-badge" id="cart-quantity">0</span>
                    </button>

                    @if(session('customer_id'))
                    <a href="{{ route('orders.history') }}" class="btn btn-orders">
                        <i class="bi bi-clock-history me-1"></i> My Orders
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>


   <!-- Modern Cart Offcanvas -->
    <div class="offcanvas offcanvas-end offcanvas-cart" tabindex="-1" id="cartOffcanvas">
        <!-- Header -->
        <div class="offcanvas-header cart-header">
            <h5 class="offcanvas-title mb-0">
                <i class="bi bi-cart-check-fill me-2"></i> Your Cart
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>

        <!-- Body -->
        <div class="offcanvas-body p-0 d-flex flex-column">
            
            <!-- Delivery Info -->
            <div class="delivery-info-section d-none" id="deliveryInfoSection">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">Order Type</small>
                        <strong id="selected-delivery-type" class="text-primary fs-6"></strong>
                    </div>
                    <button type="button" class="btn btn-change-delivery" id="changeDeliveryType">
                        <i class='bx bx-edit me-1'></i> Change
                    </button>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="row g-0 text-center">
                    <div class="col-6">
                        <small class="d-block text-muted mb-1">Total Items</small>
                        <h5 class="mb-0 fw-bold text-dark" id="cart-item-count">0 items</h5>
                    </div>
                    <div class="col-6">
                        <small class="d-block text-muted mb-1">Total Amount</small>
                        <h4 class="mb-0 fw-bold text-primary" id="cart-total-amount">RM 0.00</h4>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="cart-items-container">
                <ul class="cart-list list-unstyled mb-0">
                    <li class="empty-cart">
                        <i class="bi bi-cart-x"></i>
                        <p class="mt-2 mb-0">Your cart is empty</p>
                        <small class="text-muted">Add items to get started</small>
                    </li>
                </ul>
            </div>

            <!-- Order Details -->
            <div class="order-details-section">
                <div class="mb-3 d-none" id="tableNumberSection">
                    <label class="form-label">
                        <i class="bi bi-table me-1"></i> Table Number <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="table_number" class="form-control" placeholder="Enter table number">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-telephone me-1"></i> Contact Number <span class="text-danger">*</span>
                    </label>
                    <input type="tel" name="customer_contact" class="form-control" placeholder="0123456789" required>
                </div>
                
                <!-- Optional Contact Number -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-telephone-plus me-1"></i> Additional Contact Number <span class="text-muted">(Optional)</span>
                    </label>
                    <input type="tel" name="additional_contact" class="form-control" placeholder="Optional second contact number">
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i> In case we need to reach you at another number
                    </small>
                </div>

                <!-- Delivery Address -->
                <div class="mb-3 d-none" id="addressSection">
                    <label class="form-label">
                        <i class="bi bi-geo-alt me-1"></i> Delivery Address <span class="text-danger">*</span>
                    </label>
                    <textarea name="customer_address" class="form-control" rows="3" placeholder="Enter your full delivery address" required></textarea>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i> This address will be saved to your profile
                    </small>
                </div>

                <!-- Cash Note -->
                <div class="alert alert-warning py-2 mb-3 d-none" id="cashNote">
                    <small><i class="bi bi-cash-coin me-1"></i> <strong>Note:</strong> Cash payment only</small>
                </div>

                <!-- Confirm Button -->
                <div>
                    <small class="text-muted d-block text-center mb-2">
                        Please review your order before confirming
                    </small>
                    <button type="button" class="btn btn-success btn-lg w-100 gradient-button confirm-order" disabled>
                        <i class="bi bi-check-circle me-1"></i> Confirm Order
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
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%);">
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
                <div class="modal-header" style="background: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%);">
                    <h5 class="modal-title text-white"><i class="bi bi-credit-card me-2"></i> Payment Method</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
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
                    <button type="button" class="btn btn-primary" id="confirmPayment" style="background: linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%); border: none;">
                        <i class="bi bi-check-circle me-1"></i> Confirm Payment
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
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-cart-check-fill fs-1 text-warning d-block mb-3"></i>
                    <p id="finalConfirmationMessage" class="fs-6 text-start" style="white-space: pre-line;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="finalConfirmOrder">
                        <i class="bi bi-check-circle me-1"></i> Yes, Confirm Order
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
                    <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i> Success!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <p id="successMessage" class="fs-5 mt-3 fw-semibold"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="closeSuccessModal" data-bs-dismiss="modal">
                        <i class="bi bi-check me-1"></i> OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @yield('content')

    <!-- Footer -->
    @if(!isset($hideLayout) || !$hideLayout)
    <footer class="footer text-white py-5">
        <div class="container">
            <div class="row gy-4 justify-content-between">

                <!-- Logo -->
                <div class="col-md-3 text-center text-md-start">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="MadrasDarbar" class="img-fluid mb-3" style="max-height: 120px; border-radius: 15px;">
                </div>

                <!-- Our Locations -->
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Our Locations</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Pattaya</a></li>
                        <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Bangkok</a></li>
                        <li class="mb-2"><a href="" class="text-white-50 text-decoration-none">Phuket</a></li>
                        <li><a href="" class="text-white-50 text-decoration-none">Colombo</a></li>
                    </ul>
                </div>

                <!-- Contact Us -->
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Contact Us</h5>
                    <p class="text-white-50 mb-2">
                        <i class="bi bi-envelope-fill me-2"></i>
                        <a href="mailto:madrasdarbar@gmail.com" class="text-white-50 text-decoration-none">madrasdarbar@gmail.com</a>
                    </p>
                    <p class="text-white-50 mb-4">
                        <i class="bi bi-envelope-fill me-2"></i>
                        <a href="mailto:info@madrasdarbar.org" class="text-white-50 text-decoration-none">info@madrasdarbar.org</a>
                    </p>

                    <!-- Social Icons -->
                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/madrasdarbarsrilanka" target="_blank" class="social-icon text-white text-decoration-none">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/madrasdarbarsrilanka?igsh=MWRjZGV2bnN6NzNtdA==" target="_blank" class="social-icon text-white text-decoration-none">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="text-center mt-4 border-top pt-3 border-light">
                <small class="text-white-50">© {{ date('Y') }} Madras Darbar. All Rights Reserved.</small>
            </div>
        </div>
    </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/public.js') }}"></script>

</body>
</html>