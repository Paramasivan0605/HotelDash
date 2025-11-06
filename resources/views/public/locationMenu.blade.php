@extends('main')

@section('title', 'Menu - ' . $location->location_name)

@section('content')
@if(session('restore_cart'))
    <script>
        // Logic to restore the user's cart from backend or local storage
        alert("Welcome back! We've restored your cart.");

        // TODO: fetch cart items for this user via AJAX, or load from localStorage
    </script>
@endif

@if(session('pending_order'))
    <div class="alert alert-warning mt-4">
        <i class="bi bi-exclamation-triangle"></i>
        You still have an order in progress. 
        <a href="{{ route('orders.details', session('pending_order')) }}" class="alert-link">View Order</a>
    </div>
@endif

<!-- Delivery Option Modal -->
<div class="modal fade" id="deliveryOptionModal" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
      <div class="modal-body p-0">
        <div class="delivery-modal-header">
          <div class="icon-container">
            <i class='bx bx-food-menu'></i>
          </div>
          <h3 class="modal-title">Choose Your Order Type</h3>
          <p class="modal-subtitle">Select how you'd like to receive your order</p>
        </div>
        
        <div class="delivery-options-grid">
          <button class="delivery-option-card" data-option="Doorstep Delivery" data-location-id="{{ $location->location_id }}">
            <div class="option-icon">🚗</div>
            <h4>Doorstep Delivery</h4>
            <p>We'll bring it to you</p>
            <div class="option-badge">Most Popular</div>
          </button>
          
          <button class="delivery-option-card" data-option="Counter Pickup" data-location-id="{{ $location->location_id }}">
            <div class="option-icon">🏪</div>
            <h4>Counter Pickup</h4>
            <p>Pick up from restaurant</p>
            <div class="option-badge">Fast & Easy</div>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Location Header --}}
<div class="location-header-section">
    <div class="location-header-overlay"></div>
    <div class="container py-5 position-relative" style="margin-top: 3rem;">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="location-info-card">
                    <div class="location-icon">
                        <i class='bx bx-map-pin'></i>
                    </div>
                    <div class="location-details">
                        <span class="location-badge">📍 Now Serving</span>
                        <h1 class="location-name">{{ $location->location_name }}</h1>
                        <div class="location-meta">
                            <span class="currency-pill">
                                <i class='bx bx-dollar-circle'></i>
                                {{ $location->currency }}
                            </span>
                            <span class="location-status">
                                <i class='bx bx-time-five'></i>
                                Open Now
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-4 text-lg-end">
                <a href="{{ route('menu') }}" class="btn-change-location">
                    <i class='bx bx-transfer'></i>
                    <span>Change Location</span>
                </a>
            </div> --}}
        </div>
    </div>
</div>

{{-- Food Menu Section --}}
<div id="menuSection" class="menu-container" style="display:none;">
    <div class="container py-5">
        <div class="menu-section-header">
            <div class="section-title-wrapper">
                <h2 class="section-title">
                    <span class="title-icon">🍽️</span>
                    Our Delicious Menu
                </h2>
                <p class="section-subtitle">Handcrafted with love, served with passion</p>
            </div>
            <div class="menu-filters">
                <button class="filter-btn active" data-filter="all">
                    <i class='bx bx-grid-alt'></i> All Items
                </button>
            </div>
        </div>
        
        @if($foodMenu->isEmpty())
            <div class="empty-menu-state">
                <div class="empty-icon">🍽️</div>
                <h3>No Menu Items Available</h3>
                <p>Check back soon for delicious options!</p>
                {{-- <a href="{{ route('menu') }}" class="btn-primary-custom">
                    Browse Other Locations
                </a> --}}
            </div>
        @else
            <div class="food-menu-grid">
                @foreach($foodMenu as $item)
                    <div class="food-item-card" data-aos="fade-up">
                        <div class="card-image-wrapper" data-bs-toggle="modal" data-bs-target="#itemModal{{ $item->id }}">
                            <img src="{{ $item->image ? asset($item->image) : asset('images/placeholder.jpg') }}" 
                                 alt="{{ $item->name }}" 
                                 class="food-image">
                            @if($item->category)
                                <span class="food-category">{{ $item->category }}</span>
                            @endif
                            <div class="image-overlay">
                                <div class="overlay-content">
                                    <i class='bx bx-show'></i>
                                    <span>Quick View</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-content">
                            <h3 class="food-name">{{ $item->name }}</h3>
                            
                            @if($item->description)
                                <p class="food-description">{{ Str::limit($item->description, 60) }}</p>
                            @endif
                            
                            <div class="card-footer-content">
                                <div class="price-display">
                                    <span class="currency-symbol">{{ $location->currency }}</span>
                                    <span class="price-amount">{{ number_format($item->price, 2) }}</span>
                                </div>
                                
                                <button class="btn-add-to-cart" 
                                        data-food-id="{{ $item->id }}" 
                                        data-food-image="{{ asset($item->image) }}" 
                                        data-food-name="{{ $item->name }}" 
                                        data-food-price="{{ $item->price }}"
                                        data-delivery-type=""
                                        data-location-id="">
                                    <i class='bx bx-cart-add'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Enhanced Modal --}}
                    <div class="modal fade food-detail-modal" id="itemModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                                    <i class='bx bx-x'></i>
                                </button>
                                
                                <div class="row g-0">
                                    <div class="col-lg-6">
                                        <div class="modal-image-section">
                                            <img src="{{ $item->image ? asset($item->image) : asset('images/placeholder.jpg') }}" 
                                                 alt="{{ $item->name }}">
                                            @if($item->category)
                                                <span class="modal-category-badge">{{ $item->category }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="modal-content-section">
                                            <div class="modal-header-content">
                                                <h2 class="modal-food-name">{{ $item->name }}</h2>
                                                <div class="rating-stars">
                                                    <i class='bx bxs-star'></i>
                                                    <i class='bx bxs-star'></i>
                                                    <i class='bx bxs-star'></i>
                                                    <i class='bx bxs-star'></i>
                                                    <i class='bx bxs-star-half'></i>
                                                    <span>(4.5)</span>
                                                </div>
                                            </div>
                                            
                                            @if($item->description)
                                                <div class="modal-description">
                                                    <h4>About This Dish</h4>
                                                    <p>{{ $item->description }}</p>
                                                </div>
                                            @endif
                                            
                                            <div class="modal-highlights">
                                                <div class="highlight-item">
                                                    <i class='bx bx-time-five'></i>
                                                    <span>15-20 mins</span>
                                                </div>
                                                <div class="highlight-item">
                                                    <i class='bx bx-fire'></i>
                                                    <span>Freshly Made</span>
                                                </div>
                                                <div class="highlight-item">
                                                    <i class='bx bx-check-shield'></i>
                                                    <span>Quality Assured</span>
                                                </div>
                                            </div>
                                            
                                            <div class="modal-price-section">
                                                <div class="price-label">Price</div>
                                                <div class="price-value">
                                                    <span class="currency">{{ $location->currency }}</span>
                                                    <span class="amount">{{ number_format($item->price, 2) }}</span>
                                                </div>
                                            </div>
                                            
                                            <button type="button" 
                                                    class="btn-modal-add-cart add-to-cart"
                                                    data-food-id="{{ $item->id }}" 
                                                    data-food-image="{{ asset($item->image) }}" 
                                                    data-food-name="{{ $item->name }}" 
                                                    data-food-price="{{ $item->price }}"
                                                    data-delivery-type="{{ $item->delivery_type }}"
                                                    data-location-id="{{ $location->location_id }}"
                                                    data-bs-dismiss="modal">
                                                <i class='bx bx-cart'></i>
                                                <span>Add to Cart</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    /* Modern Red Color Palette */
    :root {
        --primary-red: #dc2626;
        --secondary-red: #ef4444;
        --dark-red: #991b1b;
        --light-red: #fee2e2;
        --accent-orange: #f97316;
        --red-gradient: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
        --dark-gradient: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
    }

    /* Delivery Modal Styles */
    .delivery-modal-header {
        background: var(--red-gradient);
        padding: 3rem 2rem 2rem;
        text-align: center;
        color: white;
        position: relative;
    }

    .icon-container {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        animation: bounceIn 0.6s ease;
    }

    @keyframes bounceIn {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        60% {
            transform: translateY(-10px);
        }
    }

    .modal-title {
        font-size: 2rem;
        font-weight: 800;
        margin: 0 0 0.5rem;
        letter-spacing: -0.5px;
    }

    .modal-subtitle {
        margin: 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .delivery-options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
    }

    .delivery-option-card {
        background: white;
        border: 3px solid #e5e7eb;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .delivery-option-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--red-gradient);
        transition: left 0.3s ease;
        z-index: 0;
    }

    .delivery-option-card:hover::before {
        left: 0;
    }

    .delivery-option-card:hover {
        border-color: var(--primary-red);
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(220, 38, 38, 0.25);
    }

    .delivery-option-card:hover * {
        color: white;
        position: relative;
        z-index: 1;
    }

    .option-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        filter: grayscale(0.3);
        transition: all 0.3s ease;
    }

    .delivery-option-card:hover .option-icon {
        filter: grayscale(0);
        transform: scale(1.2) rotate(10deg);
    }

    .delivery-option-card h4 {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0.5rem 0;
        color: #1f2937;
        transition: color 0.3s ease;
    }

    .delivery-option-card p {
        color: #6b7280;
        margin: 0;
        transition: color 0.3s ease;
    }

    .option-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--red-gradient);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
    }

    /* Location Header Section */
    .location-header-section {
        background: var(--red-gradient);
        position: relative;
        min-height: 280px;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .location-header-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></svg>');
        background-size: 100px 100px;
        opacity: 0.3;
    }

    .location-info-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .location-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        flex-shrink: 0;
    }

    .location-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .location-name {
        font-size: 2.5rem;
        font-weight: 900;
        color: white;
        margin: 0.5rem 0;
        line-height: 1.2;
        letter-spacing: -1px;
    }

    .location-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .currency-pill,
    .location-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .btn-change-location {
        background: white;
        color: var(--primary-red);
        padding: 1rem 2rem;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-change-location:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        color: var(--dark-red);
    }

    /* Menu Section */
    .menu-container {
        background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
        min-height: 100vh;
    }

    .menu-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3rem;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .section-title-wrapper {
        flex: 1;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: #1f2937;
        margin: 0 0 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        letter-spacing: -1px;
    }

    .title-icon {
        font-size: 3rem;
    }

    .section-subtitle {
        color: #6b7280;
        font-size: 1.1rem;
        margin: 0;
    }

    .menu-filters {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: white;
        border: 2px solid #e5e7eb;
        color: #4b5563;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--red-gradient);
        border-color: var(--primary-red);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.25);
    }

    /* Food Menu Grid */
    .food-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
    }

    .food-item-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .food-item-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 50px rgba(220, 38, 38, 0.2);
    }

    .card-image-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
        cursor: pointer;
    }

    .food-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .food-item-card:hover .food-image {
        transform: scale(1.15) rotate(2deg);
    }

    .food-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--red-gradient);
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 2;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(220, 38, 38, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .card-image-wrapper:hover .image-overlay {
        opacity: 1;
    }

    .overlay-content {
        color: white;
        text-align: center;
        transform: translateY(20px);
        transition: transform 0.4s ease;
    }

    .card-image-wrapper:hover .overlay-content {
        transform: translateY(0);
    }

    .overlay-content i {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .overlay-content span {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .card-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .food-name {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0 0 0.75rem;
        line-height: 1.3;
    }

    .food-description {
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0 0 1rem;
        flex: 1;
    }

    .card-footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 2px solid #f3f4f6;
    }

    .price-display {
        display: flex;
        align-items: baseline;
        gap: 0.3rem;
        color: var(--primary-red);
        font-weight: 900;
    }

    .currency-symbol {
        font-size: 1.1rem;
    }

    .price-amount {
        font-size: 1.8rem;
    }

    .btn-add-to-cart {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--red-gradient);
        border: none;
        color: white;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    }

    .btn-add-to-cart:hover {
        transform: rotate(360deg) scale(1.15);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.5);
    }

    /* Modal Styles */
    .food-detail-modal .modal-content {
        border: none;
        border-radius: 32px;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    }

    .modal-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        border: none;
        font-size: 1.8rem;
        color: var(--primary-red);
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close-btn:hover {
        background: var(--red-gradient);
        color: white;
        transform: rotate(90deg) scale(1.1);
    }

    .modal-image-section {
        height: 100%;
        min-height: 500px;
        position: relative;
    }

    .modal-image-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-category-badge {
        position: absolute;
        top: 25px;
        left: 25px;
        background: var(--red-gradient);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-size: 0.95rem;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
    }

    .modal-content-section {
        padding: 3rem;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .modal-food-name {
        font-size: 2.5rem;
        font-weight: 900;
        color: #1f2937;
        margin: 0;
        letter-spacing: -1px;
    }

    .rating-stars {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        color: #fbbf24;
        font-size: 1.3rem;
        margin-top: 0.5rem;
    }

    .rating-stars span {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .modal-description h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.75rem;
    }

    .modal-description p {
        color: #6b7280;
        font-size: 1.05rem;
        line-height: 1.8;
    }

    .modal-highlights {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        padding: 1.5rem;
        background: #f9fafb;
        border-radius: 16px;
    }

    .highlight-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #4b5563;
        font-weight: 600;
    }

    .highlight-item i {
        font-size: 1.5rem;
        color: var(--primary-red);
    }

    .modal-price-section {
        background: var(--red-gradient);
        padding: 1.5rem 2rem;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .price-label {
        font-size: 1.1rem;
        font-weight: 600;
        opacity: 0.9;
    }

    .price-value {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        font-weight: 900;
    }

    .price-value .currency {
        font-size: 1.3rem;
    }

    .price-value .amount {
        font-size: 2.5rem;
    }

    .btn-modal-add-cart {
        background: var(--dark-gradient);
        color: white;
        border: none;
        padding: 1.25rem 2.5rem;
        border-radius: 16px;
        font-size: 1.2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(153, 27, 27, 0.3);
    }

    .btn-modal-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(153, 27, 27, 0.4);
    }

    .btn-modal-add-cart i {
        font-size: 1.5rem;
    }

    /* Empty State */
    .empty-menu-state {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-icon {
        font-size: 6rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .empty-menu-state h3 {
        font-size: 2rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .empty-menu-state p {
        color: #6b7280;
        font-size: 1.2rem;
        margin-bottom: 2rem;
    }

    .btn-primary-custom {
        background: var(--red-gradient);
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 700;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
    }

    .btn-primary-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(220, 38, 38, 0.4);
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .food-menu-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 992px) {
        .location-name {
            font-size: 2rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .modal-image-section {
            min-height: 400px;
        }

        .modal-content-section {
            padding: 2rem;
        }

        .modal-food-name {
            font-size: 2rem;
        }

        .delivery-options-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .location-info-card {
            flex-direction: column;
            text-align: center;
        }

        .location-name {
            font-size: 1.75rem;
        }

        .location-meta {
            justify-content: center;
        }

        .menu-section-header {
            flex-direction: column;
            text-align: center;
        }

        .section-title {
            font-size: 1.75rem;
            justify-content: center;
        }

        .food-menu-grid {
            grid-template-columns: 1fr;
        }

        .btn-change-location {
            width: 100%;
            justify-content: center;
        }

        .modal-content-section {
            padding: 1.5rem;
        }

        .modal-food-name {
            font-size: 1.75rem;
        }

        .modal-highlights {
            flex-direction: column;
            gap: 1rem;
        }

        .price-value .amount {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .location-header-section {
            min-height: 240px;
        }

        .location-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }

        .location-name {
            font-size: 1.5rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .title-icon {
            font-size: 2rem;
        }

        .card-image-wrapper {
            height: 200px;
        }

        .food-name {
            font-size: 1.2rem;
        }

        .price-amount {
            font-size: 1.5rem;
        }

        .btn-add-to-cart {
            width: 45px;
            height: 45px;
            font-size: 1.3rem;
        }

        .modal-image-section {
            min-height: 300px;
        }

        .modal-title {
            font-size: 1.5rem;
        }

        .modal-subtitle {
            font-size: 1rem;
        }

        .icon-container {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }

        .delivery-option-card {
            padding: 1.5rem;
        }

        .option-icon {
            font-size: 3rem;
        }

        .delivery-option-card h4 {
            font-size: 1.1rem;
        }
    }

    /* Smooth Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .food-item-card {
        animation: fadeInUp 0.6s ease backwards;
    }

    .food-item-card:nth-child(1) { animation-delay: 0.1s; }
    .food-item-card:nth-child(2) { animation-delay: 0.2s; }
    .food-item-card:nth-child(3) { animation-delay: 0.3s; }
    .food-item-card:nth-child(4) { animation-delay: 0.4s; }
    .food-item-card:nth-child(5) { animation-delay: 0.5s; }
    .food-item-card:nth-child(6) { animation-delay: 0.6s; }

    /* Loading State */
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 12px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--red-gradient);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--dark-gradient);
    }

    /* Focus States for Accessibility */
    button:focus-visible,
    a:focus-visible {
        outline: 3px solid var(--primary-red);
        outline-offset: 3px;
    }

    /* Print Styles */
    @media print {
        .topbar,
        .btn-change-location,
        .btn-add-to-cart,
        .btn-modal-add-cart {
            display: none;
        }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection