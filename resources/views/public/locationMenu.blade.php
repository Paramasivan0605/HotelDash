@extends('main')

@section('title', 'Menu - ' . $location->location_name)

@section('content')
<script>
    window.locationCurrency = "{{ $currency ?? 'RM' }}";
</script>

@if(session('restore_cart'))
    <script>
        alert("Welcome back! We've restored your cart.");
    </script>
@endif

@if(session('pending_order'))
    <div class="alert alert-warning mt-4">
        <i class="bi bi-exclamation-triangle"></i>
        You still have an order in progress. 
        <a href="{{ route('orders.history', session('pending_order')) }}" class="alert-link">View Order</a>
    </div>
@endif

<!-- Delivery Option Modal -->
<div class="modal fade" id="deliveryOptionModal" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="delivery-modal-header">
          <div class="icon-container">
            <i class='bx bx-food-menu'></i>
          </div>
          <h3 class="modal-title-custom">Choose Your Order Type</h3>
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

{{-- Header Section --}}
<div class="top-bar clean-header">
    <div class="location-clean">
        <span class="deliver-label">Delivering to</span>
        <span class="deliver-location">{{ $location->location_name }}</span>
    </div>
</div>



    {{-- Search Bar --}}


    {{-- Banner Section --}}
    {{-- Banner Section - Updated for Beautiful Desktop + Perfect Mobile --}}
<div class="banner-section">
    <div class="container-fluid p-0">
        <div class="banner-wrapper">
            @php
                $banner = \App\Models\Banner::latest()->first();
            @endphp

            @if($banner && $banner->image)
                <img src="{{ asset($banner->image) }}" 
                     alt="Promotion Banner" 
                     class="banner-image-desktop">
            @else
                <img src="{{ asset('images/default-banner.jpg') }}" 
                     alt="Default Banner" 
                     class="banner-image-desktop">
            @endif
        </div>
    </div>
</div>
        <div class="search-container-wrapper">
        <div class="search-box">
            <i class='bx bx-search'></i>
            <input type="text" placeholder='Search for dishes...' class="search-input">
            <i class='bx bx-microphone microphone-icon'></i>
        </div>
    </div>
</div>

{{-- Categories Section --}}
@if(!$categories->isEmpty())
<div class="categories-section">
    <div class="categories-container">
        <div class="categories-scroll">
            <div class="categories-list">
                <button class="category-item active" data-category="all">
                    <div class="category-icon-wrapper">🍽️</div>
                    <span>All Items</span>
                </button>
                
                @foreach($categories as $category)
                    <button class="category-item" data-category="{{ $category->id }}">
                        <div class="category-icon-wrapper">
                            @if($category->image)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                            @else
                                🍴
                            @endif
                        </div>
                        <span>{{ $category->name }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Menu Section --}}
<div class="full-bg-wrapper">

    <div id="menuSection" class="menu-section">
        <div class="menu-container-wrapper">
            <div class="menu-header">
                <h2 class="menu-title" id="categoryTitle">Our Delicious Menu</h2>
            </div>
            
            @if($foodMenu->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">🍽️</div>
                    <h3>No Menu Items Available</h3>
                    <p>Check back soon for delicious options!</p>
                </div>
            @else
                <div class="menu-grid">
                    @foreach($foodMenu as $item)
                        @php
                            $categoryImage = '';
                            if($item->category_id && $categories->where('id', $item->category_id)->first()) {
                                $category = $categories->where('id', $item->category_id)->first();
                                $categoryImage = $category->image ? asset($category->image) : '';
                            }
                        @endphp
                        
                        <div class="menu-card" data-category="{{ $item->category_id ?? 'uncategorized' }}">
                            <div class="menu-card-body">
                                @if($item->category)
                                    <div class="menu-card-tag">{{ $item->category }}</div>
                                @endif
                                
                                <h3 class="menu-card-title">{{ $item->name }}</h3>
                                
                                {{-- @if($item->description)
                                    <p class="menu-card-description">{{ Str::limit($item->description, 50) }}</p>
                                @endif --}}
                                
                                <div class="menu-card-bottom">
                                    <div class="menu-card-price">
                                        <span class="price-value">{{ $location->currency }} {{ number_format($item->price, 2) }}</span>
                                    </div>
                                    
                                    <button class="add-btn-new" 
                                            data-food-id="{{ $item->id }}" 
                                            data-food-name="{{ $item->name }}" 
                                            data-food-price="{{ $item->price }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#itemModal{{ $item->id }}">
                                        <i class='bx bx-plus'></i>
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Item Detail Modal --}}
                        <div class="modal fade" id="itemModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content item-modal">
                                    <div class="modal-header">
                                        <div class="modal-header-info">
                                            <i class='bx bx-dish'></i>
                                            <h3 class="modal-title">{{ $item->name }}</h3>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                            <i class='bx bx-x'></i>
                                        </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        @if($item->category)
                                            <div class="modal-category">
                                                <i class='bx bx-category-alt'></i>
                                                <span>{{ $item->category }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($item->description)
                                            <div class="modal-description">
                                                <div class="description-label">
                                                    <i class='bx bx-info-circle'></i>
                                                    <span>About this dish</span>
                                                </div>
                                                <p>{{ $item->description }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="modal-price-section">
                                            <div class="price-label">
                                                <i class='bx bx-purchase-tag'></i>
                                                <span>Price</span>
                                            </div>
                                            <span class="price-amount">
                                                {{ $location->currency }} {{ number_format($item->price, 2) }}
                                            </span>
                                        </div>
                                        
                                        <button type="button" 
                                                class="add-to-cart-btn add-to-cart"
                                                data-food-id="{{ $item->id }}" 
                                                data-food-name="{{ $item->name }}" 
                                                data-food-price="{{ $item->price }}"
                                                data-food-category="{{ $item->category ?? '' }}"
                                                data-food-description="{{ $item->description ?? '' }}"
                                                data-category-image="{{ $categoryImage }}"
                                                data-delivery-type=""
                                                data-location-id="{{ $location->location_id }}"
                                                data-bs-dismiss="modal">
                                            <i class='bx bx-cart-add'></i>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
<style>
    .full-bg-wrapper {
    background: url('/mnt/data/28dcc8a0-6cf8-4280-b368-27032544a3be.png');
    background-size: cover;      /* covers full area like screenshot */
    background-position: center; /* centers image */
    background-repeat: no-repeat;
    padding-top: 2rem;
    padding-bottom: 2rem;
}

:root {
    --primary-red: #dc2626;
    --dark-red: #991b1b;
    --light-red: #fee2e2;
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-500: #6b7280;
    --gray-800: #1f2937;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Container */
.top-bar-container,
.search-container-wrapper,
.banner-container,
.categories-container,
.menu-container-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.top-bar-container {
    padding-top: 1rem;
    padding-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.search-container-wrapper {
    margin-top: -1.5rem;
    position: relative;
    z-index: 10;
}

.banner-container {
    padding: 0 1rem;
}

.categories-container {
    padding: 1rem;
}

.menu-container-wrapper {
    padding: 2rem 1rem;
}

/* Flex Utilities */
.top-bar-left,
.top-bar-right {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.logo-img {
    height: 40px;
}
/* === Beautiful Banner - Desktop & Mobile Perfect === */
.banner-section {
    margin: 1rem 0 1.5rem;
    padding: 0 1rem;
    border-radius: 1.5rem;
    overflow: hidden;
}

.banner-wrapper {
    position: relative;
    width: 100%;
    height: 280px; /* Desktop height */
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(220, 38, 38, 0.15);
    border: 4px solid #fff;
}

.banner-image-desktop {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.banner-image-desktop:hover {
    transform: scale(1.05);
}

/* Mobile Optimization */
@media (max-width: 768px) {
    .banner-section {
        margin: 0.75rem 0 1.25rem;
        padding: 0 0.75rem;
    }

    .banner-wrapper {
        height: 200px;
        border-radius: 1.2rem;
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.12);
        border: 3px solid #fff;
    }
}

@media (max-width: 480px) {
    .banner-wrapper {
        height: 180px;
        border-radius: 1rem;
    }
}
.cart-btn,
.profile-btn {
    position: relative;
}

.cart-btn i,
.profile-btn i {
    font-size: 2rem;
}

/* Header Styles */
.header-wrapper {
    background: transparent !important;
}
.top-bar {
    background: transparent !important;
    box-shadow: none !important;
    padding: 0.5rem 0;  
}

.new-top-left {
    align-items: flex-start;
}

.logo-img,
.profile-btn,
.top-bar-right {
    display: none !important;
}


.top-bar {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: var(--white);
}

.location-label {
    font-size: 0.75rem;
    opacity: 0.9;
}

.location-name-text {
    font-weight: 700;
    font-size: 1.125rem;
    margin-top: 0.25rem;
}

.location-address {
    font-size: 0.75rem;
    opacity: 0.85;
    margin-top: 0.125rem;
}

.cart-btn, .profile-btn {
    background: transparent;
    border: none;
    color: var(--white);
    cursor: pointer;
    transition: transform 0.2s;
}

.cart-btn:hover, .profile-btn:hover {
    transform: scale(1.1);
}
.location-clean span {
    color: #e20a0f !important;
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--white);
    color: var(--primary-red);
    font-size: 0.75rem;
    font-weight: 700;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Search Bar */
.search-container {
    margin-top: -1.5rem;
    position: relative;
    z-index: 10;
}
.search-container-wrapper {
    margin-top: 1rem !important;
    margin-bottom: 1.5rem;
}

.search-box {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 2px solid var(--gray-100);
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 1rem;
    color: var(--gray-800);
}

.search-input::placeholder {
    color: var(--gray-500);
}

.search-box i {
    font-size: 1.25rem;
}
/* CLEAN MODERN HEADER */
.clean-header {
    background: transparent !important;
    padding: 10px 15px;
    display: flex;
    justify-content: flex-start;
    align-items: flex-end;
    height: 70px;
    backdrop-filter: blur(2px);
    position: relative;
    z-index: 10;
}

.location-clean {
    display: flex;
    flex-direction: column;
    color: #ffffff;
    margin-left: 5px;
}

.deliver-label {
    font-size: 12px;
    opacity: 0.8;
}

.deliver-location {
    font-size: 22px;
    font-weight: 700;
    line-height: 20px;
}

/* REMOVE LOGO + CART + ICON AREA */
.top-bar-left img,
.top-bar-right,
.profile-btn {
    display: none !important;
}

/* REMOVE WHITE BACKGROUND ABOVE */
.header-wrapper {
    background: transparent !important;
    box-shadow: none !important;
}

.search-box .bx-search {
    color: var(--gray-500);
}

.microphone-icon {
    color: var(--primary-red);
    cursor: pointer;
}

/* Banner */
.banner-section {
    padding: 2rem 0;
}

.banner-image-wrapper {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.banner-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

/* Categories */
.categories-section {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}


.categories-scroll {
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-red) var(--gray-100);
}

.categories-scroll::-webkit-scrollbar {
    height: 6px;
}

.categories-scroll::-webkit-scrollbar-track {
    background: var(--gray-100);
}

.categories-scroll::-webkit-scrollbar-thumb {
    background: var(--primary-red);
    border-radius: 10px;
}

.categories-list {
    display: flex;
    gap: 1rem;
    min-width: min-content;
}

.category-item {
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: 1rem;
    padding: 0.75rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s;
    font-weight: 600;
    color: var(--gray-800);
}

.category-item:hover,
.category-item.active {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: var(--white);
    transform: translateY(-2px);
}

.category-icon-wrapper {
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-icon-wrapper img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

/* Menu Section */
.menu-section {
    background: transparent !important; /* allow background image to show */
    min-height: 100vh;
}


.menu-header {
    text-align: center;
    margin-bottom: 2rem;
}

.menu-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--gray-800);
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    max-width: 800px;
    margin: 0 auto;
}

.menu-card {
    background: rgba(255, 255, 255, 0.8); /* slight transparency */
    backdrop-filter: blur(6px);    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    border: 1px solid var(--gray-100);
}

.menu-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
    border-color: var(--primary-red);
}

.menu-card.hidden {
    display: none;
}

.menu-card-body {
    padding: 0.875rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.menu-card-tag {
    background: var(--light-red);
    color: var(--primary-red);
    font-size: 0.625rem;
    font-weight: 700;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    width: fit-content;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.menu-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.4rem;
}

.menu-card-description {
    font-size: 0.7rem;
    color: var(--gray-500);
    line-height: 1.3;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.menu-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.25rem;
    padding-top: 0.625rem;
    border-top: 1px solid var(--gray-100);
}

.menu-card-price {
    display: flex;
    flex-direction: column;
}

.price-value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--primary-red);
}

.add-btn-new {
    background: var(--primary-red);
    color: var(--white);
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    cursor: pointer;
    transition: all 0.3s;
}

.add-btn-new:hover {
    background: var(--dark-red);
    transform: scale(1.05);
}

.add-btn-new i {
    font-size: 1rem;
}

/* Modal Styles */
.item-modal .modal-content {
    border: none;
    border-radius: 1.5rem;
    overflow: hidden;
}

.item-modal .modal-header {
    background: var(--white);
    border-bottom: 3px solid var(--primary-red);
    padding: 1.5rem;
}

.modal-header-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-header-info i {
    font-size: 2rem;
    color: var(--primary-red);
}

.item-modal .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
}

.item-modal .btn-close {
    background: var(--primary-red);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    border: none;
    cursor: pointer;
}

.item-modal .modal-body {
    padding: 1.5rem;
}

.modal-category {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--light-red);
    color: var(--dark-red);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.modal-description {
    margin-bottom: 1rem;
    background: var(--gray-50);
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 4px solid var(--primary-red);
}

.description-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
}

.modal-description p {
    color: var(--gray-500);
    line-height: 1.6;
    margin: 0;
}

.modal-price-section {
    background: var(--primary-red);
    padding: 1rem 1.25rem;
    border-radius: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.price-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--white);
    font-weight: 600;
}

.price-amount {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--white);
}

.add-to-cart-btn {
    width: 100%;
    background: var(--dark-red);
    color: var(--white);
    border: none;
    padding: 1rem;
    border-radius: 0.75rem;
    font-size: 1.125rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.3s;
}

.add-to-cart-btn:hover {
    background: var(--primary-red);
    transform: translateY(-2px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--white);
    border-radius: 1rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--gray-500);
}

/* Delivery Modal */
.delivery-modal-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    padding: 2rem;
    text-align: center;
    color: var(--white);
}

.icon-container {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
}

.modal-title-custom {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.modal-subtitle {
    opacity: 0.9;
}

.delivery-options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 1.5rem;
}

.delivery-option-card {
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: 1rem;
    padding: 1.5rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.delivery-option-card:hover {
    border-color: var(--primary-red);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.2);
}

.option-icon {
    font-size: 3rem;
    margin-bottom: 0.75rem;
}

.delivery-option-card h4 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0.5rem 0;
    color: var(--gray-800);
}

.delivery-option-card p {
    color: var(--gray-500);
    margin: 0;
    font-size: 0.875rem;
}

.option-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--primary-red);
    color: var(--white);
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    font-size: 0.625rem;
    font-weight: 700;
}

/* Responsive */
@media (max-width: 768px) {
    .menu-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.625rem;
    }
    
    .delivery-options-grid {
        grid-template-columns: 1fr;
    }
    
    .banner-image {
        height: 180px;
    }

    .menu-card-title {
        font-size: 0.875rem;
    }

    .menu-card-description {
        font-size: 0.675rem;
    }

    .add-btn-new {
        padding: 0.375rem 0.625rem;
        font-size: 0.75rem;
    }

    .price-value {
        font-size: 0.9rem;
    }

    .menu-card-body {
        padding: 0.75rem;
    }
}

@media (max-width: 480px) {
    .menu-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .menu-card-title {
        font-size: 0.8rem;
        min-height: 2rem;
    }

    .menu-card-body {
        padding: 0.625rem;
    }

    .add-btn-new {
        padding: 0.3rem 0.5rem;
        font-size: 0.7rem;
        gap: 0.2rem;
    }

    .price-value {
        font-size: 0.85rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show menu section on page load
    const menuSection = document.getElementById('menuSection');
    if (menuSection) {
        menuSection.style.display = 'block';
    }

    // Category filtering
    const categoryCards = document.querySelectorAll('.category-item');
    const foodItems = document.querySelectorAll('.menu-card');
    const categoryTitle = document.getElementById('categoryTitle');
    
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const selectedCategory = this.getAttribute('data-category');
            
            // Update active state
            categoryCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            // Filter menu items
            let visibleCount = 0;
            foodItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                
                if (selectedCategory === 'all') {
                    item.classList.remove('hidden');
                    visibleCount++;
                    categoryTitle.textContent = 'Our Delicious Menu';
                } else {
                    if (itemCategory === selectedCategory) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                    const categoryName = this.querySelector('span').textContent;
                    categoryTitle.textContent = categoryName;
                }
            });

            // Show menu section if hidden
            if (menuSection) {
                menuSection.style.display = 'block';
            }
            
            // Scroll to menu section
            menuSection.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });

            console.log(`Filtered to category: ${selectedCategory}, showing ${visibleCount} items`);
        });
    });

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get data from button
            const foodId = this.getAttribute('data-food-id');
            const foodName = this.getAttribute('data-food-name');
            const foodPrice = this.getAttribute('data-food-price');
            const deliveryType = this.getAttribute('data-delivery-type') || '';
            const locationId = this.getAttribute('data-location-id');
            
            // Check if delivery type is selected (if required)
            if (!deliveryType) {
                // Show delivery option modal first
                const deliveryModal = new bootstrap.Modal(document.getElementById('deliveryOptionModal'));
                deliveryModal.show();
                
                // Store item data for later
                sessionStorage.setItem('pendingCartItem', JSON.stringify({
                    foodId: foodId,
                    foodName: foodName,
                    foodPrice: foodPrice,
                    locationId: locationId
                }));
                
                return;
            }
            
            // Add to cart logic
            addItemToCart(foodId, foodName, foodPrice, deliveryType, locationId);
        });
    });

    // Handle delivery option selection
    const deliveryOptions = document.querySelectorAll('.delivery-option-card');
    
    deliveryOptions.forEach(option => {
        option.addEventListener('click', function() {
            const deliveryType = this.getAttribute('data-option');
            const locationId = this.getAttribute('data-location-id');
            
            // Get pending item from session storage
            const pendingItem = sessionStorage.getItem('pendingCartItem');
            
            if (pendingItem) {
                const item = JSON.parse(pendingItem);
                addItemToCart(item.foodId, item.foodName, item.foodPrice, deliveryType, locationId);
                sessionStorage.removeItem('pendingCartItem');
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deliveryOptionModal'));
            modal.hide();
        });
    });

    // Fix for modal aria-hidden focus issue
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            // Remove focus from any buttons inside the modal
            const focusedElement = this.querySelector(':focus');
            if (focusedElement) {
                focusedElement.blur();
            }
        });
        
        modal.addEventListener('hide.bs.modal', function() {
            // Remove focus before hiding
            const focusedElement = this.querySelector(':focus');
            if (focusedElement) {
                focusedElement.blur();
            }
        });
    });
});
</script>

@endsection