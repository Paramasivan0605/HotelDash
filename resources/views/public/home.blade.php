<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MadrasDarbar - Food Ordering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-red: #E74C3C;
            --primary-red-dark: #C0392B;
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
        }

        /* Header Styles */
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

        .navbar-brand img {
            height: 40px;
        }

        .restaurant-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0;
        }

        .min-order-badge {
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-buttons .btn {
            border: 2px solid var(--border-color);
            background-color: white;
            color: var(--text-dark);
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .filter-buttons .btn.active {
            background-color: var(--primary-red);
            color: white;
            border-color: var(--primary-red);
        }

        /* Sidebar Styles */
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

        /* Content Area */
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

        /* Menu Item Card */
        .menu-card {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            position: relative;
        }

        .menu-card:hover {
            box-shadow: 0 4px 16px rgba(231, 76, 60, 0.15);
            transform: translateY(-2px);
        }

        .menu-card-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }

        .veg-icon, .non-veg-icon {
            width: 18px;
            height: 18px;
            border: 2px solid;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }

        .veg-icon {
            border-color: #27AE60;
        }

        .veg-icon::after {
            content: '';
            width: 8px;
            height: 8px;
            background-color: #27AE60;
            border-radius: 50%;
        }

        .non-veg-icon {
            border-color: var(--primary-red);
        }

        .non-veg-icon::after {
            content: '';
            width: 8px;
            height: 8px;
            background-color: var(--primary-red);
            border-radius: 50%;
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
        }

        .item-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .availability-badge {
            background-color: #FEF5F4;
            color: var(--primary-red);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
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

        /* Quantity Controls */
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

        /* Cart Sidebar */
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

        /* Cart Float Button */
        .cart-float-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: var(--primary-red);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(231, 76, 60, 0.4);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s;
        }

        .cart-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.5);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #2C3E50;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        /* Overlay */
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

        /* Mobile Styles */
        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }

            .mobile-category-menu {
                display: block !important;
                background-color: white;
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 20px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            }

            .mobile-category-select {
                width: 100%;
                padding: 12px;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                font-size: 15px;
                background-color: white;
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

            .cart-sidebar {
                width: 100%;
                right: -100%;
            }

            .cart-float-btn {
                bottom: 20px;
                right: 20px;
            }
        }

        @media (max-width: 576px) {
            .menu-card-img {
                width: 100%;
                height: 180px;
                margin-right: 0;
                margin-bottom: 15px;
            }

            .item-name {
                font-size: 16px;
            }

            .item-price {
                font-size: 18px;
            }
        }

        .d-lg-none {
            display: none;
        }

        @media (max-width: 991px) {
            .d-lg-none {
                display: block;
            }
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="d-flex align-items-center">
                <i class="bi bi-telephone-fill me-2"></i>
                <span>04448614871</span>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar sticky-top">
        <div class="container">
            <div class="d-flex align-items-center w-100 justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="navbar-brand me-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="MadrasDarbar">
                    </div>
                    <div>
                        <p class="restaurant-name mb-0">MadrasDarbar</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="min-order-badge d-none d-md-block">
                        <span>Min Order: ₹ 400.00</span>
                    </div>
                    <button class="btn btn-outline-secondary d-none d-md-inline-flex" type="button">
                        <i class="bi bi-search me-2"></i> Search
                    </button>
                    <div class="filter-buttons d-none d-md-flex gap-2">
                        <button class="btn active">Veg</button>
                        <button class="btn">Non Veg</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Min Order -->
    <div class="container d-md-none mt-3">
        <div class="min-order-badge w-100 text-center">
            Min Order: ₹ 400.00
        </div>
    </div>

    <!-- Mobile Filters -->
    <div class="container d-md-none mt-3">
        <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-sm border active" style="flex: 1;">
                <i class="bi bi-circle-fill text-success me-1" style="font-size: 10px;"></i> Veg
            </button>
            <button class="btn btn-sm border" style="flex: 1;">
                <i class="bi bi-circle-fill text-danger me-1" style="font-size: 10px;"></i> Non Veg
            </button>
            <button class="btn btn-sm border" style="flex: 1;">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar">
                    <h5 class="sidebar-title">Menu Categories</h5>
                    <div class="category-item active">Value Combo</div>
                    <div class="category-item">Indian</div>
                    <div class="category-item">Indian Soup</div>
                    <div class="category-item">Tandoori</div>
                    <div class="category-item">Starters</div>
                    <div class="category-item">Chinese</div>
                    <div class="category-item">Juice</div>
                    <div class="category-item">Dessert</div>
                    <div class="category-item">Pick-Up</div>
                    <div class="category-item">Rolls</div>
                    <div class="category-item">Chinese Soups</div>
                    <div class="category-item">Biriyani</div>
                    <div class="category-item">Bucket & Box Biriyani</div>
                </div>
            </div>

            <!-- Mobile Category Dropdown -->
            <div class="col-12 d-lg-none mb-3">
                <div class="mobile-category-menu">
                    <select class="mobile-category-select">
                        <option>Value Combo</option>
                        <option>Indian</option>
                        <option>Indian Soup</option>
                        <option>Tandoori</option>
                        <option>Starters</option>
                        <option>Chinese</option>
                        <option>Juice</option>
                        <option>Dessert</option>
                        <option>Pick-Up</option>
                        <option>Rolls</option>
                        <option>Chinese Soups</option>
                        <option>Biriyani</option>
                        <option>Bucket & Box Biriyani</option>
                    </select>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <div class="content-area">
                    <h2 class="section-title">Value Combo</h2>

                    <!-- Menu Item 1 -->
                    <div class="menu-card">
                        <div class="row align-items-center">
                            <div class="col-md-3 col-12">
                                <img src="https://via.placeholder.com/300x300/8B4513/FFFFFF?text=Biriyani+BBQ" class="menu-card-img w-100" alt="Biriyani Bbq Combo">
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="d-flex align-items-start">
                                    <span class="non-veg-icon"></span>
                                    <div>
                                        <h5 class="item-name">Biriyani Bbq Combo</h5>
                                        <p class="item-description">Biriyani Rice +BBQ a qtr +Raitha+Gravy</p>
                                        <div class="item-price">₹ 360.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                <button class="btn-add" onclick="addToCart('Biriyani Bbq Combo', 360)">
                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Item 2 -->
                    <div class="menu-card">
                        <div class="row align-items-center">
                            <div class="col-md-3 col-12">
                                <img src="https://via.placeholder.com/300x300/DAA520/FFFFFF?text=Chicken+65" class="menu-card-img w-100" alt="Biriyani Chicken 65 Combo">
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="d-flex align-items-start">
                                    <span class="non-veg-icon"></span>
                                    <div>
                                        <h5 class="item-name">Biriyani Chicken 65 Combo</h5>
                                        <p class="item-description">Biriyani Rice with 1pcs of chicken+chicken 65 3pcs+Raitha+Gravy</p>
                                        <div class="item-price">₹ 360.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                <button class="btn-add" onclick="addToCart('Biriyani Chicken 65 Combo', 360)">
                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Item 3 -->
                    <div class="menu-card">
                        <div class="row align-items-center">
                            <div class="col-md-3 col-12">
                                <img src="https://via.placeholder.com/300x300/8B4513/FFFFFF?text=Tandoori" class="menu-card-img w-100" alt="Biriyani Tandoor Combo">
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="d-flex align-items-start">
                                    <span class="non-veg-icon"></span>
                                    <div>
                                        <h5 class="item-name">Biriyani Tandoor Combo</h5>
                                        <p class="item-description">Biriyani Rice+Tandoori qtr+Raitha+Gravy</p>
                                        <div class="item-price">₹ 360.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                <button class="btn-add" onclick="addToCart('Biriyani Tandoor Combo', 360)">
                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Item 4 -->
                    <div class="menu-card">
                        <div class="row align-items-center">
                            <div class="col-md-3 col-12">
                                <img src="https://via.placeholder.com/300x300/CD853F/FFFFFF?text=Paratha+BBQ" class="menu-card-img w-100" alt="Paratha Bbq Combo">
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="d-flex align-items-start">
                                    <span class="non-veg-icon"></span>
                                    <div>
                                        <h5 class="item-name">Paratha Bbq Combo</h5>
                                        <p class="item-description">Paratha with BBQ qtr and Gravy</p>
                                        <div class="item-price">₹ 320.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                <button class="btn-add" onclick="addToCart('Paratha Bbq Combo', 320)">
                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Item 5 -->
                    <div class="menu-card">
                        <div class="row align-items-center">
                            <div class="col-md-3 col-12">
                                <img src="https://via.placeholder.com/300x300/CD853F/FFFFFF?text=Paratha+Tandoor" class="menu-card-img w-100" alt="Paratha Tandoor Combo">
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="d-flex align-items-start">
                                    <span class="non-veg-icon"></span>
                                    <div>
                                        <h5 class="item-name">Paratha Tandoor Combo</h5>
                                        <p class="item-description">Paratha with Tandoori qtr and Gravy</p>
                                        <div class="item-price">₹ 320.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                <button class="btn-add" onclick="addToCart('Paratha Tandoor Combo', 320)">
                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Item 6 -->
                    <div class="menu-card">
                        <div class="row align-items-center">
                            <div class="col-md-3 col-12">
                                <img src="https://via.placeholder.com/300x300/DAA520/FFFFFF?text=Chicken+65+Paratha" class="menu-card-img w-100" alt="Paratha Chicken 65 Combo">
                            </div>
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <div class="d-flex align-items-start">
                                    <span class="non-veg-icon"></span>
                                    <div>
                                        <h5 class="item-name">Paratha Chicken 65 Combo</h5>
                                        <p class="item-description">Paratha with Chicken 65 and Gravy</p>
                                        <div class="item-price">₹ 320.00</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 text-md-end text-center mt-3 mt-md-0">
                                <button class="btn-add" onclick="addToCart('Paratha Chicken 65 Combo', 320)">
                                    Add <i class="bi bi-plus-lg ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Cart Float Button -->
    <div class="cart-float-btn" onclick="toggleCart()">
        <i class="bi bi-bag-fill" style="font-size: 24px;"></i>
        <span class="cart-badge" id="cartCount">0</span>
    </div>

    <!-- Cart Sidebar -->
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
                <textarea class="form-control" rows="3" placeholder="Enter any additional information about your order..."></textarea>
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

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="toggleCart()"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];

        function addToCart(name, price) {
            const existingItem = cart.find(item => item.name === name);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
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
                                <button onclick="decrementItem(${index})">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="text" value="${item.quantity}" readonly>
                                <button onclick="incrementItem(${index})">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <button class="btn btn-sm btn-link text-danger" onclick="removeItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            cartBody.innerHTML = html;
            cartCount.textContent = totalItems;
            cartTotal.textContent = total.toFixed(2);
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
        }

        function showCartNotification() {
            const btn = document.querySelector('.cart-float-btn');
            btn.style.transform = 'scale(1.2)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 200);
        }

        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            alert('Proceeding to checkout...');
            // Here you would typically redirect to checkout page
            // window.location.href = '/checkout';
        }

        // Category selection
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('active'));
                this.classList.add('active');
                // Here you would load the selected category items
            });
        });

        // Mobile category dropdown
        document.querySelector('.mobile-category-select')?.addEventListener('change', function() {
            // Here you would load the selected category items
            console.log('Selected category:', this.value);
        });

        // Filter buttons
        document.querySelectorAll('.filter-buttons .btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-buttons .btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>