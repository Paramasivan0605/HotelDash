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
        </div>
    </div>
</div>

{{-- Categories Section --}}
@if(!$categories->isEmpty())
<div class="categories-section">
    <div class="container py-4">
        <div class="categories-scroll-container">
            <div class="categories-grid">
                <button class="category-card active" data-category="all">
                    <div class="category-icon">🍽️</div>
                    <h4>All Items</h4>
                </button>
                
                @foreach($categories as $category)
                    <button class="category-card" data-category="{{ $category->id }}">
                        <div class="category-icon">
                            @if($category->image)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                            @else
                                🍴
                            @endif
                        </div>
                        <h4>{{ $category->name }}</h4>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Food Menu Section --}}
<div id="menuSection" class="menu-container" style="display:none;">
    <div class="container py-5">
        <div class="menu-section-header">
            <h2 class="section-title" id="categoryTitle">Our Delicious Menu</h2>
        </div>
        
        @if($foodMenu->isEmpty())
            <div class="empty-menu-state">
                <div class="empty-icon">🍽️</div>
                <h3>No Menu Items Available</h3>
                <p>Check back soon for delicious options!</p>
            </div>
        @else
            <div class="food-menu-grid">
                @foreach($foodMenu as $item)
                    @php
                        $categoryImage = '';
                        if($item->category_id && $categories->where('id', $item->category_id)->first()) {
                            $category = $categories->where('id', $item->category_id)->first();
                            $categoryImage = $category->image ? asset($category->image) : '';
                        }
                    @endphp
                    
                    <div class="food-item-card" data-category="{{ $item->category_id ?? 'uncategorized' }}">
                        <div class="card-background">
                            @if($categoryImage)
                                <img src="{{ $categoryImage }}" alt="{{ $item->category ?? 'Food Item' }}" class="category-bg-image">
                            @else
                                <div class="animated-gradient-bg"></div>
                            @endif
                        </div>
                        
                        <div class="card-content-wrapper">
                            @if($item->category)
                                <div class="item-category-tag">
                                    <i class='bx bx-food-tag'></i>
                                    {{ $item->category }}
                                </div>
                            @endif
                            
                            <div class="item-info">
                                <h3 class="item-name">{{ $item->name }}</h3>
                                
                                <div class="item-footer">
                                    <div class="item-price">
                                        <span class="currency">{{ $location->currency }}</span>
                                        <span class="price">{{ number_format($item->price, 2) }}</span>
                                    </div>
                                    
                                    <button class="btn-add-item" 
                                            data-food-id="{{ $item->id }}" 
                                            data-food-name="{{ $item->name }}" 
                                            data-food-price="{{ $item->price }}"
                                            data-food-category="{{ $item->category ?? '' }}"
                                            data-food-description="{{ $item->description ?? '' }}"
                                            data-category-image="{{ $categoryImage }}"
                                            data-delivery-type=""
                                            data-location-id=""
                                            data-bs-toggle="modal" 
                                            data-bs-target="#itemModal{{ $item->id }}">
                                        <i class='bx bx-cart-add'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Enhanced Modal with Background --}}
                    <div class="modal fade item-modal" id="itemModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-background">
                                    @if($categoryImage)
                                        <img src="{{ $categoryImage }}" alt="{{ $item->category ?? 'Food Item' }}">
                                    @else
                                        <div class="animated-gradient-bg"></div>
                                    @endif
                                </div>
                                
                                <div class="modal-header">
                                    <div class="modal-header-content">
                                        <i class='bx bx-dish modal-icon'></i>
                                        <h3 class="modal-title">{{ $item->name }}</h3>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
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
                                            <div class="description-header">
                                                <i class='bx bx-info-circle'></i>
                                                <span>About this dish</span>
                                            </div>
                                            <div class="description-content">
                                                {{ $item->description }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="modal-price-box">
                                        <div class="price-box-header">
                                            <i class='bx bx-purchase-tag'></i>
                                            <span>Price</span>
                                        </div>
                                        <span class="modal-price-value">
                                            {{ $location->currency }} {{ number_format($item->price, 2) }}
                                        </span>
                                    </div>
                                    
                                    <button type="button" 
                                            class="btn-confirm-add add-to-cart"
                                            data-food-id="{{ $item->id }}" 
                                            data-food-name="{{ $item->name }}" 
                                            data-food-price="{{ $item->price }}"
                                            data-delivery-type="{{ $item->delivery_type ?? '' }}"
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

<style>
    /* Color Palette */
    :root {
        --primary-red: #dc2626;
        --secondary-red: #ef4444;
        --dark-red: #991b1b;
        --light-red: #fee2e2;
        --accent-orange: #f97316;
        --red-gradient: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
        --dark-gradient: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
    }

    /* Delivery Modal */
    .delivery-modal-header {
        background: var(--red-gradient);
        padding: 2.5rem 2rem 2rem;
        text-align: center;
        color: white;
    }

    .icon-container {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.2rem;
        animation: bounceIn 0.6s ease;
    }

    @keyframes bounceIn {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-20px); }
        60% { transform: translateY(-10px); }
    }

    .modal-title-custom {
        font-size: 1.75rem;
        font-weight: 800;
        margin: 0 0 0.5rem;
    }

    .modal-subtitle {
        margin: 0;
        opacity: 0.9;
        font-size: 1rem;
    }

    .delivery-options-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
        padding: 1.75rem;
    }

    .delivery-option-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.75rem 1.25rem;
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
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(220, 38, 38, 0.25);
    }

    .delivery-option-card:hover * {
        color: white;
        position: relative;
        z-index: 1;
    }

    .option-icon {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .delivery-option-card:hover .option-icon {
        transform: scale(1.15);
    }

    .delivery-option-card h4 {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0.5rem 0;
        color: #1f2937;
        transition: color 0.3s ease;
    }

    .delivery-option-card p {
        color: #6b7280;
        margin: 0;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .option-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--red-gradient);
        color: white;
        padding: 0.35rem 0.7rem;
        border-radius: 15px;
        font-size: 0.7rem;
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
        background-image: url('{{ asset('images/bg-image.jpeg') }}');
        background-size: 100px 100px;
        opacity: 0.3;
    }

    .location-info-card {
        background: var(--red-gradient);
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

    /* Categories */
    .categories-section {
        background: white;
        border-bottom: 2px solid #f3f4f6;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .categories-scroll-container {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-red) #f3f4f6;
    }

    .categories-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .categories-scroll-container::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 10px;
    }

    .categories-scroll-container::-webkit-scrollbar-thumb {
        background: var(--red-gradient);
        border-radius: 10px;
    }

    .categories-grid {
        display: flex;
        gap: 1rem;
        padding: 0.5rem 0;
        min-width: min-content;
    }

    .category-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1rem 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        white-space: nowrap;
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
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

    .category-card:hover::before,
    .category-card.active::before {
        left: 0;
    }

    .category-card:hover,
    .category-card.active {
        border-color: var(--primary-red);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.25);
    }

    .category-card:hover *,
    .category-card.active * {
        color: white;
        position: relative;
        z-index: 1;
    }

    .category-icon {
        font-size: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .category-icon img {
        width: 35px;
        height: 35px;
        object-fit: cover;
        border-radius: 50%;
    }

    .category-card h4 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1f2937;
        transition: color 0.3s ease;
    }

    /* Menu Section */
    .menu-container {
        background-image: url('{{ asset('images/bg-image.jpeg') }}');
        min-height: 100vh;
        padding: 2rem 0;
    }

    .menu-section-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* Food Menu Grid */
    .food-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .food-item-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        border: 2px solid transparent;
        min-height: 180px;
    }

    .food-item-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(220, 38, 38, 0.2);
        border-color: var(--primary-red);
    }

    .food-item-card.hidden {
        display: none;
    }

    .card-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
        overflow: hidden;
    }

    .category-bg-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(8px) brightness(0.7);
        transform: scale(1.1);
        transition: all 0.5s ease;
    }

    .food-item-card:hover .category-bg-image {
        filter: blur(10px) brightness(0.6);
        transform: scale(1.15);
    }

    .animated-gradient-bg {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
        filter: blur(8px);
        animation: gradientShift 3s ease infinite;
        background-size: 200% 200%;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .card-content-wrapper {
        position: relative;
        z-index: 1;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        min-height: 180px;
    }

    .item-category-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--red-gradient);
        color: white;
        padding: 0.5rem 1.1rem;
        border-radius: 25px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: fit-content;
        box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
    }

    .item-category-tag i {
        font-size: 1rem;
    }

    .item-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 1;
    }

    .item-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0;
        line-height: 1.3;
    }

    .item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 2px solid #f3f4f6;
        margin-top: auto;
    }

    .item-price {
        display: flex;
        align-items: baseline;
        gap: 0.3rem;
        color: var(--primary-red);
        font-weight: 900;
    }

    .item-price .currency {
        font-size: 1rem;
    }

    .item-price .price {
        font-size: 1.75rem;
    }

    .btn-add-item {
        width: 48px;
        height: 48px;
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
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.35);
    }

    .btn-add-item:hover {
        transform: scale(1.15) rotate(90deg);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
    }

    .btn-add-item i {
        transition: transform 0.3s ease;
    }

    /* Modal */
    .item-modal .modal-content {
        border: none;
        border-radius: 24px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        position: relative;
    }

    .modal-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
    }

    .modal-background img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(20px) brightness(0.5);
    }

    .item-modal .modal-header {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(15px);
        color: #1f2937;
        padding: 1.75rem 2rem;
        border: none;
        border-radius: 24px 24px 0 0;
        position: relative;
        z-index: 1;
        border-bottom: 3px solid var(--primary-red);
    }

    .modal-header-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modal-icon {
        font-size: 2rem;
        color: var(--primary-red);
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .item-modal .modal-title {
        font-size: 1.65rem;
        font-weight: 900;
        margin: 0;
        color: #1f2937;
    }

    .item-modal .btn-close {
        background: var(--red-gradient);
        border-radius: 50%;
        opacity: 1;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        color: white;
    }

    .item-modal .btn-close:hover {
        transform: rotate(90deg) scale(1.1);
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
    }

    .item-modal .modal-body {
        padding: 2rem;
        position: relative;
        z-index: 1;
        /* background: rgba(255, 255, 255, 0.98); */
        backdrop-filter: blur(15px);
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-category {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: var(--dark-red);
        padding: 0.85rem 1.35rem;
        border-radius: 15px;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        border: 2px solid var(--light-red);
    }

    .modal-category i {
        font-size: 1.4rem;
        color: var(--primary-red);
    }

    .modal-description {
        margin-bottom: 1.5rem;
        background: #f9fafb;
        padding: 1.25rem;
        border-radius: 12px;
        border-left: 4px solid var(--primary-red);
    }

    .description-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .description-header i {
        font-size: 1.3rem;
        color: var(--primary-red);
    }

    .description-content {
        color: #6b7280;
        font-size: 1rem;
        line-height: 1.7;
        margin: 0;
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-height: 150px;
        overflow-y: auto;
        padding-right: 5px;
    }

    /* Scrollbar for description */
    .description-content::-webkit-scrollbar {
        width: 4px;
    }

    .description-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .description-content::-webkit-scrollbar-thumb {
        background: var(--primary-red);
        border-radius: 10px;
    }

    .modal-price-box {
        background: var(--red-gradient);
        padding: 1.35rem 1.65rem;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
    }

    .price-box-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }

    .price-box-header i {
        font-size: 1.5rem;
    }

    .modal-price-value {
        font-size: 2rem;
        font-weight: 900;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .btn-confirm-add {
        width: 100%;
        background: var(--dark-gradient);
        color: white;
        border: none;
        padding: 1.15rem;
        border-radius: 14px;
        font-size: 1.15rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(153, 27, 27, 0.35);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-confirm-add:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(153, 27, 27, 0.45);
    }

    .btn-confirm-add i {
        font-size: 1.5rem;
        animation: cartShake 1s infinite;
    }

    @keyframes cartShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-3px); }
        75% { transform: translateX(3px); }
    }

    /* Empty State */
    .empty-menu-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .empty-menu-state h3 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .empty-menu-state p {
        color: #6b7280;
        font-size: 1.1rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .delivery-options-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .location-info-card {
            flex-direction: column;
            text-align: center;
        }

        .food-menu-grid {
            grid-template-columns: 1fr;
            padding: 0 0.5rem;
        }

        .section-title {
            font-size: 1.65rem;
        }

        .item-modal .modal-body {
            padding: 1.5rem;
            max-height: 60vh;
        }
    }

    @media (max-width: 576px) {
        .location-name {
            font-size: 1.65rem;
        }

        .section-title {
            font-size: 1.4rem;
        }

        .item-modal .modal-body {
            padding: 1.25rem;
        }

        .modal-price-value {
            font-size: 1.5rem;
        }

        .description-content {
            max-height: 120px;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .food-item-card {
        animation: fadeInUp 0.5s ease backwards;
    }

    .food-item-card:nth-child(1) { animation-delay: 0.05s; }
    .food-item-card:nth-child(2) { animation-delay: 0.1s; }
    .food-item-card:nth-child(3) { animation-delay: 0.15s; }
    .food-item-card:nth-child(4) { animation-delay: 0.2s; }
    .food-item-card:nth-child(5) { animation-delay: 0.25s; }
    .food-item-card:nth-child(6) { animation-delay: 0.3s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryCards = document.querySelectorAll('.category-card');
    const foodItems = document.querySelectorAll('.food-item-card');
    const categoryTitle = document.getElementById('categoryTitle');
    
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const selectedCategory = this.getAttribute('data-category');
            
            categoryCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            foodItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                
                if (selectedCategory === 'all') {
                    item.classList.remove('hidden');
                    categoryTitle.textContent = 'Our Delicious Menu';
                } else {
                    if (itemCategory === selectedCategory) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                    const categoryName = this.querySelector('h4').textContent;
                    categoryTitle.textContent = categoryName;
                }
            });
            
            document.getElementById('menuSection').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        });
    });
});
</script>

@endsection