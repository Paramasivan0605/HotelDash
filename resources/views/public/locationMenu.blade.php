@extends('main')

@section('title', 'Menu - ' . $location->location_name)

@section('content')

<!-- Delivery Option Modal -->
<div class="modal fade" id="deliveryOptionModal" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <h4 class="mb-3" id="deliveryOptionModalLabel">Choose Your Order Type</h4>
      <p class="text-muted mb-4">Please select one to continue</p>
      <div class="d-flex flex-column gap-3">
        <button class="btn btn-outline-primary delivery-option-btn" data-option="Doorstep Delivery">
          🛵 Doorstep Delivery
        </button>
        <button class="btn btn-outline-success delivery-option-btn" data-option="Restaurant Dine-in">
          🍽️ Restaurant Dine-in
        </button>
        <button class="btn btn-outline-warning delivery-option-btn" data-option="Counter Pickup">
          🧾 Counter Pickup
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Location Header --}}
<div class="location-header">
    <div class="container-fluid px-4 py-4" style="margin-top:5%">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-3">
                    <div class="location-icon-box">
                        <i class='bx bx-map'></i>
                    </div>
                    <div>
                        <h1 class="mb-1">{{ $location->location_name }}</h1>
                        <span class="currency-tag">{{ $location->currency }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('menu') }}" class="btn-back">
                <i class='bx bx-arrow-back'></i> Change Location
            </a>
        </div>
    </div>
</div>
`
{{-- Food Menu Section --}}
<div id="menuSection" class="container-fluid px-4 py-4" style="display:none;">
    <div class="section-title">
        <h2>Our Delicious Menu</h2>
        <p>Click on any item to view details</p>
    </div>
    
    @if($foodMenu->isEmpty())
        <div class="empty-state">
            <i class='bx bx-food-menu'></i>
            <p>No menu items available in this location.</p>
        </div>
    @else
        <div class="menu-grid">
            @foreach($foodMenu as $item)
                <div class="food-card" data-bs-toggle="modal" data-bs-target="#itemModal{{ $item->id }}">
                    <div class="food-card-image">
                        <img src="{{ $item->image ? asset($item->image) : asset('images/placeholder.jpg') }}" alt="{{ $item->name }}">
                        @if($item->category)
                            <span class="category-badge">{{ $item->category }}</span>
                        @endif
                        <div class="hover-overlay">
                            <i class='bx bx-show'></i>
                            <span>View Details</span>
                        </div>
                    </div>
                    <div class="food-card-content">
                        <h3>{{ Str::limit($item->name, 30) }}</h3>
                        <div class="price-tag">
                            <span class="currency">{{ $location->currency }}</span>
                            <span class="amount">{{ number_format($item->price, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Modal --}}
                <div class="modal fade" id="itemModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content food-modal">
                            <button type="button" class="modal-close" data-bs-dismiss="modal">
                                <i class='bx bx-x'></i>
                            </button>
                            
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <div class="modal-image">
                                        <img src="{{ $item->image ? asset($item->image) : asset('images/placeholder.jpg') }}" alt="{{ $item->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="modal-details">
                                        @if($item->category)
                                            <span class="modal-category">{{ $item->category }}</span>
                                        @endif
                                        
                                        <h2>{{ $item->name }}</h2>
                                        
                                        @if($item->description)
                                            <p class="description">{{ $item->description }}</p>
                                        @endif
                                        
                                        <div class="modal-price">
                                            <span class="currency">{{ $location->currency }}</span>
                                            <span class="amount">{{ number_format($item->price, 2) }}</span>
                                        </div>
                                        
                                        <button type="button" 
                                                class="btn-add-cart add-to-cart"
                                                data-food-id="{{ $item->id }}" 
                                                data-food-image="{{ asset($item->image) }}" 
                                                data-food-name="{{ $item->name }}" 
                                                data-food-price="{{ $item->price }}"
                                                data-delivery-type=""
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

<style>
    /* Header Styles */
    .location-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .location-icon-box {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .location-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .currency-tag {
        background: rgba(255,255,255,0.25);
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-back {
        background: white;
        color: #667eea;
        padding: 10px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Section Title */
    .section-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .section-title h2 {
        font-size: 32px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .section-title p {
        color: #718096;
        font-size: 16px;
    }

    /* Menu Grid */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 30px;
    }

    /* Food Card */
    .food-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .food-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .food-card-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .food-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .food-card:hover .food-card-image img {
        transform: scale(1.1);
    }

    .category-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #ef4444;
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 2;
    }

    .hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(102, 126, 234, 0.95);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.3s;
        color: white;
    }

    .hover-overlay i {
        font-size: 40px;
    }

    .hover-overlay span {
        font-weight: 600;
        font-size: 14px;
    }

    .food-card:hover .hover-overlay {
        opacity: 1;
    }

    .food-card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .food-card-content h3 {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin: 0 0 12px 0;
        line-height: 1.4;
    }

    .price-tag {
        display: flex;
        align-items: baseline;
        gap: 4px;
        color: #667eea;
        font-weight: 700;
    }

    .price-tag .currency {
        font-size: 16px;
    }

    .price-tag .amount {
        font-size: 24px;
    }

    /* Modal Styles */
    .food-modal {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s;
    }

    .modal-close:hover {
        transform: rotate(90deg);
        background: #ef4444;
        color: white;
    }

    .modal-image {
        height: 100%;
        min-height: 400px;
    }

    .modal-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-details {
        padding: 40px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        height: 100%;
    }

    .modal-category {
        display: inline-block;
        background: #fef2f2;
        color: #ef4444;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        align-self: flex-start;
    }

    .modal-details h2 {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        line-height: 1.3;
    }

    .modal-details .description {
        color: #718096;
        font-size: 15px;
        line-height: 1.7;
        margin: 0;
        flex: 1;
    }

    .modal-price {
        display: flex;
        align-items: baseline;
        gap: 6px;
        color: #667eea;
        font-weight: 700;
        padding: 20px 0;
        border-top: 2px solid #e2e8f0;
        border-bottom: 2px solid #e2e8f0;
    }

    .modal-price .currency {
        font-size: 20px;
    }

    .modal-price .amount {
        font-size: 36px;
    }

    .btn-add-cart {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 16px 32px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-add-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-add-cart i {
        font-size: 22px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-state i {
        font-size: 80px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .empty-state p {
        color: #718096;
        font-size: 18px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .menu-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .modal-image {
            min-height: 300px;
        }

        .modal-details {
            padding: 30px 20px;
        }

        .location-header h1 {
            font-size: 22px;
        }

        .section-title h2 {
            font-size: 24px;
        }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection