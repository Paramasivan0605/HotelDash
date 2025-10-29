<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <title>@yield('title', 'Home')</title>
</head>

<body>

    <div class="topbar">

        <div class="container">

            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Hash Logo">
                <span>Hash Restaurant</span>
            </div>

            <div class="nav-page">

                <div class="{{ request()->routeIs(['home']) ? 'active' : '' }}">
                    <a href="{{ route('home') }}"><span>Home</span></a>
                </div>

                <div class="{{ request()->routeIs(['menu', 'search']) ? 'active' : '' }}">
                    <a href="{{ route('menu') }}"><span>Menu</span></a>
                </div>

                <div class="{{ request()->routeIs(['promotion']) ? 'active' : '' }}">
                    <a href="{{ route('promotion') }}"><span>Promotions</span></a>
                </div>

                <div class="{{ request()->routeIs(['reservation']) ? 'active' : '' }}">
                    <a href="{{ route('reservation') }}"><span>Reservation</span></a>
                </div>

            </div>

            <div class="manage">
                <div class="search">
                    <i class='bx bx-search' id="open-search"></i>
                    <div class="search-container">
                        <form action="{{ route('search') }}" method="GET" id="search-form">
                            <i class='bx bx-search' id="search-button"></i>
                            <input type="text" name="search" placeholder="Search..." value="{{ old('search') }}">
                        </form>
                    </div>
                </div>

                <div class="cart">
                    <i class='bx bx-cart'></i>
                    <span class="cart-quantity" id="cart-quantity">0</span>
                </div>

                <div class="company">
                    <a href="{{ route('login') }}"><i class='bx bx-buildings'></i></a>
                </div>
            </div>

        </div>

    </div>

    <div class="cart-section">
        <div class="header">
            <span>Confirm Order</span>
            <div class="close-cart">
                <i class='bx bx-x'></i>
            </div>
        </div>

        {{-- Table Number (only for Restaurant Dine-in) --}}
        <div class="table-number" style="display:none;">
            <span>Table No.</span>
            <input type="text" name="table_number" placeholder="0">
            <div class="message"></div>
            <div id="success-response" class="success-message"></div>
            <div id="error-response" class="validation-error-message"></div>
        </div>

        {{-- Order Summary --}}
        <div class="main-section-order">
            <span>Your Order</span>
            <div class="cart-total">
                <span id="cart-item-count">Total 0 item</span>
                <span id="cart-total-amount">RM 0.00</span>
            </div>
        </div>

        {{-- List of Cart Items --}}
        <div class="your-order">
            <ul class="cart-list">
                <li><span class="empty">No item in cart</span></li>
            </ul>
        </div>

        {{-- Delivery Type Info --}}
        <div class="delivery-type-info" style="display:none;">
            <strong>Order Type:</strong> <span id="selected-delivery-type"></span>
        </div>

        {{-- Contact Number --}}
        <div class="customer-contact">
            <span>Your Contact Number <span style="color:red;">*</span></span>
            <input type="text" name="customer_contact" placeholder="0123456789" required>
        </div>

        {{-- Cash Note --}}
        <div class="cash-note" style="font-size:13px; color:#ff9800; margin-top:10px;">
            💵 <strong>Note:</strong> No online payment — Cash only.
        </div>

        {{-- Confirm Button --}}
        <div class="cart-button">
            <span>Please make sure your purchase before confirming the order.</span>
            <button type="button" class="confirm-order" disabled><span>Confirm Order</span></button>
        </div>
    </div>


    @yield('content')

    <div class="footer">

        <div class="container">

            <div class="more">
                <a href="#">About us</a>
                <a href="#">Ask question</a>
                <a href="#">Contact us</a>
            </div>

            <div class="location">
                <span>Location</span>
            </div>

            <div class="social-page">
                <div class="social-media">
                    <a href="#"><i class='bx bxl-whatsapp'></i></a>
                    <a href="#"><i class='bx bxl-facebook-circle'></i></a>
                    <a href="#"><i class='bx bxl-twitter'></i></a>
                    <a href="#"><i class='bx bxl-instagram-alt'></i></a>
                </div>
            </div>
        </div>

       

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/public.js') }}"></script>
    <!-- ✅ Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title"><i class="bi bi-check-circle-fill"></i> Success!</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
            <p id="successMessage" class="fs-5"></p>
            <i class="bi bi-cart-check-fill fs-1 text-success"></i>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-success" id="closeSuccessModal" data-bs-dismiss="modal">OK</button>
        </div>
        </div>
    </div>
    </div>

</body>

</html>
