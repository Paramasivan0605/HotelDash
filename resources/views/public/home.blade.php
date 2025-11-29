<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MadrasDarbar - Food Ordering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-red: #8B0000;
            --primary-red-dark: #6B0000;
            --primary-red-light: #F5B7B1;
            --bg-light: #F8F9FA;
            --text-dark: #2C3E50;
            --border-color: #E0E0E0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding-bottom: 80px;
        }

        .top-bar {
            background-color: var(--primary-red);
            color: white;
            padding: 10px 0;
            font-size: 14px;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 15px 0;
        }

        .restaurant-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0;
        }

        .sidebar {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .category-item {
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 15px;
            color: var(--text-dark);
            border-left: 3px solid transparent;
        }

        .category-item:hover {
            background-color: #FEF5F4;
            border-left-color: var(--primary-red-light);
        }

        .category-item.active {
            background-color: #FEF5F4;
            color: var(--primary-red);
            font-weight: 600;
            border-left-color: var(--primary-red);
        }

        .content-area {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            min-height: 600px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--text-dark);
            border-bottom: 3px solid var(--primary-red);
            display: inline-block;
            padding-bottom: 8px;
        }

        .menu-card {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .menu-card:hover {
            box-shadow: 0 4px 16px rgba(139, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .menu-card-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .item-description {
            font-size: 14px;
            color: #7F8C8D;
            margin-bottom: 12px;
            line-height: 1.5;
            max-height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .item-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .btn-add {
            background-color: var(--primary-red);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 14px;
        }

        .btn-add:hover {
            background-color: var(--primary-red-dark);
            transform: scale(1.05);
        }

        .quantity-control {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-control button {
            background-color: white;
            border: none;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--primary-red);
        }

        .quantity-control button:hover {
            background-color: #FEF5F4;
        }

        .quantity-control input {
            width: 50px;
            text-align: center;
            border: none;
            border-left: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            font-weight: 600;
        }

        .cart-sidebar {
            position: fixed;
            right: -400px;
            top: 0;
            width: 400px;
            height: 100vh;
            background-color: white;
            box-shadow: -4px 0 16px rgba(0,0,0,0.1);
            transition: right 0.3s;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }

        .cart-sidebar.show {
            right: 0;
        }

        .cart-header {
            background-color: var(--primary-red);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .close-cart {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 15px;
        }

        .cart-item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: var(--primary-red);
            font-weight: 700;
        }

        .cart-footer {
            padding: 20px;
            border-top: 2px solid var(--border-color);
            background-color: var(--bg-light);
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .btn-checkout {
            width: 100%;
            background-color: var(--primary-red);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            background-color: var(--primary-red-dark);
        }

        .cart-float-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background-color: var(--primary-red);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(139, 0, 0, 0.4);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s;
            border: 3px solid white;
        }

        .cart-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(139, 0, 0, 0.5);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #2C3E50;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            border: 2px solid white;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        .payment-option {
            cursor: pointer;
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-option .payment-card {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }

        .payment-option input[type="radio"]:checked + .payment-card {
            border-color: var(--primary-red);
            background-color: #FEF5F4;
        }

        .payment-option .payment-card:hover {
            border-color: var(--primary-red-light);
            background-color: #FEF5F4;
        }

        .payment-card i {
            font-size: 32px;
            color: var(--primary-red);
            margin-bottom: 10px;
        }

        /* Mobile Styles */
        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }

            .mobile-category-menu {
                display: block !important;
            }

            .cart-sidebar {
                width: 100%;
                right: -100%;
            }

            .cart-float-btn {
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                z-index: 1001; /* Increased z-index for mobile */
            }

            .cart-badge {
                width: 24px;
                height: 24px;
                font-size: 12px;
            }

            .content-area {
                padding: 20px;
            }

            .menu-card {
                padding: 15px;
            }

            .menu-card-img {
                width: 100px;
                height: 100px;
            }

            .item-name {
                font-size: 16px;
            }

            .item-price {
                font-size: 18px;
            }
        }

        @media (max-width: 576px) {
            .cart-float-btn {
                width: 55px;
                height: 55px;
                bottom: 15px;
                right: 15px;
            }

            .cart-badge {
                width: 20px;
                height: 20px;
                font-size: 11px;
            }

            .menu-card-img {
                width: 100%;
                height: 150px;
                margin-bottom: 10px;
            }
        }

        .category-section {
            display: none;
        }

        .category-section.active {
            display: block;
        }

        .search-bar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .cart-pulse {
            animation: pulse 0.5s ease-in-out;
        }

        /* Fix for description text overflow */
        .description-fallback {
            color: #ccc;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-telephone-fill me-2"></i>
                    <span>04448614871 | {{ strtoupper(session('order_type', 'delivery')) }}</span>
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

    <div class="container d-none" id="searchBar">
        <div class="search-bar">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for dishes..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button" onclick="performSearch()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar">
                    <h5 class="sidebar-title">Menu Categories</h5>
                    @foreach($categories as $index => $category)
                        <div class="category-item {{ $index === 0 ? 'active' : '' }}" 
                             data-category-id="{{ $category->id }}"
                             onclick="showCategory('{{ $category->id }}')">
                            {{ $category->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 d-lg-none mb-3">
                <div class="mobile-category-menu" style="background-color: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <select class="form-select" onchange="showCategory(this.value)">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="content-area">
                    @foreach($foodMenuByCategory as $categoryId => $categoryData)
                        <div class="category-section {{ $loop->first ? 'active' : '' }}" id="category-{{ $categoryId }}">
                            <h2 class="section-title">{{ $categoryData['category']->name }}</h2>
                            
                            @if($categoryData['items']->count() > 0)
                                @foreach($categoryData['items'] as $item)
                                    <div class="menu-card" data-item-name="{{ strtolower($item['name']) }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 col-12">
                                                <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/300x300/8B4513/FFFFFF?text=' . urlencode($item['name']) }}" 
                                                     class="menu-card-img w-100" alt="{{ $item['name'] }}">
                                            </div>
                                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                                <h5 class="item-name">{{ $item['name'] }}</h5>
                                                @if(!empty($item['description']) && !str_contains($item['description'], 'r6rgqeLbQaR1llPCw3V0jTC7ghng1tDrKpwuvhHNWqTQuDfxgx1pc'))
                                                    <p class="item-description">{{ $item['description'] }}</p>
                                                @else
                                                    <p class="item-description description-fallback">Delicious {{ $item['name'] }} prepared with fresh ingredients</p>
                                                @endif
                                                <div class="item-price">₹ {{ number_format($item['price'], 2) }}</div>
                                            </div>
                                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                                <button class="btn-add" 
                                                        onclick="addToCart({{ $item['id'] }}, '{{ addslashes($item['name']) }}', {{ $item['price'] }})">
                                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="mt-3 text-muted">No items available in this category</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Float Button - Fixed for mobile visibility -->
    <div class="cart-float-btn" onclick="toggleCart()">
        <i class="bi bi-bag-fill" style="font-size: 24px;"></i>
        <span class="cart-badge" id="cartCount">0</span>
    </div>

    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h5><i class="bi bi-bag-fill me-2"></i> Your Order Summary</h5>
            <button class="close-cart" onclick="toggleCart()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cart-body" id="cartBody">
            <div class="text-center text-muted py-5">
                <i class="bi bi-cart-x" style="font-size: 48px;"></i>
                <p class="mt-3">Your cart is empty</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="mb-3">
                <textarea class="form-control" rows="3" placeholder="Enter any additional information about your order..." id="orderNotes"></textarea>
            </div>
            <div class="cart-total">
                <span>Subtotal:</span>
                <span>₹ <span id="cartTotal">0.00</span></span>
            </div>
            <small class="text-muted d-block mb-3">Extra charges may apply</small>
            <button class="btn-checkout" onclick="checkout()">
                Checkout <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    <div class="overlay" id="overlay" onclick="toggleCart()"></div>

    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-red); color: white;">
                    <h5 class="modal-title"><i class="bi bi-clipboard-check me-2"></i>Complete Your Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="checkoutError"></div>
                    
                    <form id="checkoutForm">
                        <div class="mb-3">
                            <label for="customerContact" class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="customerContact" placeholder="Enter your mobile number" required>
                            <small class="text-muted">We'll use this to contact you about your order</small>
                        </div>

                        <div class="mb-3">
                            <label for="customerAddress" class="form-label fw-bold">Delivery Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="customerAddress" rows="3" placeholder="Enter your complete delivery address" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="payment-option w-100">
                                        <input type="radio" name="paymentType" value="cash" checked>
                                        <div class="payment-card">
                                            <i class="bi bi-cash-stack"></i>
                                            <div class="fw-bold">Cash</div>
                                            <small class="text-muted">Pay on delivery</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="payment-option w-100">
                                        <input type="radio" name="paymentType" value="card">
                                        <div class="payment-card">
                                            <i class="bi bi-credit-card"></i>
                                            <div class="fw-bold">Card</div>
                                            <small class="text-muted">Pay by card</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Order Total:</strong>
                                <strong style="font-size: 22px; color: var(--primary-red);">₹ <span id="modalTotal">0.00</span></strong>
                            </div>
                            <hr class="my-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Extra charges may apply for delivery
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-checkout" id="confirmOrderBtn" onclick="confirmOrder()">
                        Confirm Order <i class="bi bi-check-lg ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">Order Placed Successfully!</h4>
                    <p class="text-muted mb-3" id="successMessage"></p>
                    <div class="alert alert-light border mb-4">
                        <div class="fw-bold">Order ID</div>
                        <div style="font-size: 24px; color: var(--primary-red);" id="orderIdDisplay"></div>
                    </div>
                    <button type="button" class="btn btn-checkout" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-2"></i> Place Another Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];

        function showCategory(categoryId) {
            document.querySelectorAll('.category-section').forEach(section => {
                section.classList.remove('active');
            });
            
            const selectedSection = document.getElementById('category-' + categoryId);
            if (selectedSection) {
                selectedSection.classList.add('active');
            }

            document.querySelectorAll('.category-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.category-item[data-category-id="${categoryId}"]`)?.classList.add('active');
        }

        function addToCart(itemId, name, price) {
            const existingItem = cart.find(item => item.id === itemId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: itemId,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }
            
            updateCart();
            showCartNotification();
        }

        function updateCart() {
            const cartBody = document.getElementById('cartBody');
            const cartCount = document.getElementById('cartCount');
            const cartTotal = document.getElementById('cartTotal');
            const cartBtn = document.querySelector('.cart-float-btn');
            
            if (cart.length === 0) {
                cartBody.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-cart-x" style="font-size: 48px;"></i>
                        <p class="mt-3">Your cart is empty</p>
                    </div>
                `;
                cartCount.textContent = '0';
                cartTotal.textContent = '0.00';
                return;
            }
            
            let html = '';
            let total = 0;
            let totalItems = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                totalItems += item.quantity;
                
                html += `
                    <div class="cart-item">
                        <div class="flex-grow-1">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">₹ ${item.price.toFixed(2)}</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="quantity-control">
                                <button type="button" onclick="decrementItem(${index})">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="text" value="${item.quantity}" readonly>
                                <button type="button" onclick="incrementItem(${index})">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            cartBody.innerHTML = html;
            cartCount.textContent = totalItems;
            cartTotal.textContent = total.toFixed(2);
            
            cartBtn.classList.add('cart-pulse');
            setTimeout(() => cartBtn.classList.remove('cart-pulse'), 500);
        }

        function incrementItem(index) {
            cart[index].quantity += 1;
            updateCart();
        }

        function decrementItem(index) {
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1;
            } else {
                cart.splice(index, 1);
            }
            updateCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function toggleCart() {
            const cartSidebar = document.getElementById('cartSidebar');
            const overlay = document.getElementById('overlay');
            
            cartSidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.style.overflow = cartSidebar.classList.contains('show') ? 'hidden' : '';
        }

        function showCartNotification() {
            const btn = document.querySelector('.cart-float-btn');
            btn.style.transform = 'scale(1.2)';
            setTimeout(() => btn.style.transform = 'scale(1)', 200);
        }

        function toggleSearch() {
            document.getElementById('searchBar').classList.toggle('d-none');
        }

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => {
                const itemName = card.getAttribute('data-item-name');
                card.style.display = itemName.includes(searchTerm) ? 'block' : 'none';
            });
        }

        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('modalTotal').textContent = total.toFixed(2);
            
            const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
            checkoutModal.show();
        }

        function confirmOrder() {
            const contact = document.getElementById('customerContact').value.trim();
            const address = document.getElementById('customerAddress').value.trim();
            const paymentType = document.querySelector('input[name="paymentType"]:checked')?.value;
            const errorDiv = document.getElementById('checkoutError');
            
            errorDiv.classList.add('d-none');

            if (!contact) {
                showError('Please enter your contact number');
                return;
            }

            if (contact.length < 10) {
                showError('Please enter a valid contact number');
                return;
            }

            if (!address) {
                showError('Please enter your delivery address');
                return;
            }

            if (!paymentType) {
                showError('Please select a payment method');
                return;
            }

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            const orderData = {
                items: cart.map(item => ({
                    food_id: item.id,
                    quantity: item.quantity,
                    price: item.price
                })),
                customer_contact: contact,
                customer_address: address,
                payment_type: paymentType,
                total_amount: total,
                order_notes: document.getElementById('orderNotes').value,
                _token: '{{ csrf_token() }}'
            };

            const confirmBtn = document.getElementById('confirmOrderBtn');
            const originalBtnText = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            fetch('{{ route("checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data['success-message']) {
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    showSuccessModal(data['success-message'], data.order_code || data.order_id);
                    cart = [];
                    updateCart();
                    document.getElementById('checkoutForm').reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error['validation-error-message']) {
                    showError(error['validation-error-message']);
                } else {
                    showError('An error occurred while processing your order. Please try again.');
                }
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalBtnText;
            });
        }

        function showError(message) {
            const errorDiv = document.getElementById('checkoutError');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
            document.querySelector('#checkoutModal .modal-body').scrollTop = 0;
        }

        function showSuccessModal(message, orderId) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            document.getElementById('successMessage').textContent = message;
            document.getElementById('orderIdDisplay').textContent = '#' + orderId;
            successModal.show();
        }

        // Initialize first category as active
        document.addEventListener('DOMContentLoaded', function() {
            const firstCategory = document.querySelector('.category-item');
            if (firstCategory) {
                const categoryId = firstCategory.getAttribute('data-category-id');
                showCategory(categoryId);
            }
        });
    </script>
</body>
</html>