@extends('main')

@section('title', 'Select Location')

@section('content')

<div class="location-menu-page">

    <section>
        <main>
            <div class="page">

                {{-- Location Selection --}}
                <div class="category-banner">
                    <h1>Select Your Location</h1>
                    <p>Choose your location to explore our delicious menu</p>
                </div>

                <div class="location-selection-section">
                    <div class="location-grid">
                        @foreach($locations as $location)
                            <div class="location-card" onclick="showMenu({{ $location->location_id }}, '{{ $location->currency }}')">
                                <div class="location-icon">
                                    <i class='bx bx-map'></i>
                                </div>
                                <h3>{{ $location->location_name }}</h3>
                                <div class="currency-badge">{{ $location->currency }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Food Menu Section --}}
                <div class="signature-menu-section" id="food-menu-section" style="display:none;">
                    <div class="menu-header">
                        <h2>Available Menu Items</h2>
                        <button class="back-btn" onclick="backToLocations()">
                            <i class='bx bx-arrow-back'></i> Change Location
                        </button>
                    </div>
                    <div class="menu-grid" id="menu-grid">
                        {{-- Items will be injected via JS --}}
                    </div>
                </div>

            </div>
        </main>
    </section>

</div>

<style>
    /* Location Selection Styles */
    .location-selection-section {
        padding: 60px 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .location-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        padding: 20px 0;
    }

    .location-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 50px 30px;
        text-align: center;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .location-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: scale(0);
        transition: transform 0.5s ease;
    }

    .location-card:hover::before {
        transform: scale(1);
    }

    .location-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }

    .location-icon {
        font-size: 48px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .location-icon i {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .location-card h3 {
        font-size: 24px;
        font-weight: 600;
        margin: 15px 0;
        position: relative;
        z-index: 1;
    }

    .currency-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        z-index: 1;
    }

    /* Menu Section Styles */
    .signature-menu-section {
        padding: 60px 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .menu-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: #333;
    }

    .back-btn {
        background: #ff6b6b;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background: #ff5252;
        transform: translateX(-5px);
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        padding: 20px 0;
    }

    .menu-item {
        position: relative;
        height: 300px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .menu-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .menu-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .menu-item:hover img {
        transform: scale(1.1);
    }

    .menu-overlay {
        position: absolute;
        bottom: -8px;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 70%, transparent 100%);
        padding: 30px 25px;
        transform: translateY(60%);
        transition: transform 0.3s ease;
    }

    .menu-item:hover .menu-overlay {
        transform: translateY(0);
    }

    .menu-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 10px;
    }

    .menu-badge {
        display: inline-block;
        background: #ff6b6b;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .add-to-cart {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 6px 15px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .add-to-cart:hover {
        background: #45a049;
        transform: scale(1.05);
    }

    .add-to-cart i {
        font-size: 14px;
    }

    .menu-overlay h3 {
        color: white;
        font-size: 22px;
        font-weight: 600;
        margin: 10px 0;
        line-height: 1.3;
    }

    .menu-overlay p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
        line-height: 1.6;
        margin: 10px 0;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease 0.1s;
    }

    .menu-item:hover .menu-overlay p {
        opacity: 1;
        transform: translateY(0);
    }

    .menu-overlay .price {
        color: #ffd700;
        font-size: 24px;
        font-weight: 700;
        margin-top: 12px;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease 0.15s;
    }

    .menu-item:hover .menu-overlay .price {
        opacity: 1;
        transform: translateY(0);
    }

    .empty-message {
        text-align: center;
        padding: 60px 20px;
        color: #666;
        font-size: 18px;
    }

    .empty-message i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .location-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .location-card {
            padding: 40px 20px;
        }

        .location-icon {
            font-size: 36px;
        }

        .location-card h3 {
            font-size: 20px;
        }

        .menu-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .menu-item {
            height: 250px;
        }

        .menu-overlay h3 {
            font-size: 18px;
        }

        .menu-overlay .price {
            font-size: 20px;
        }

        .menu-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .menu-header h2 {
            font-size: 24px;
        }

        .add-to-cart span {
            display: none;
        }

        .add-to-cart {
            padding: 6px 10px;
        }
    }

    @media (max-width: 480px) {
        .location-selection-section,
        .signature-menu-section {
            padding: 40px 15px;
        }

        .menu-grid {
            grid-template-columns: 1fr;
        }

        .location-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
let selectedCurrency = 'RM'; // Default currency

function showMenu(locationId, currency) {
    selectedCurrency = currency;
    const categoryId = @json($category->id);
    
    // Show loading state
    const menuGrid = document.getElementById('menu-grid');
    menuGrid.innerHTML = '<div class="empty-message"><i class="bx bx-loader-alt bx-spin"></i><p>Loading menu items...</p></div>';
    
    const menuSection = document.getElementById('food-menu-section');
    menuSection.style.display = 'block';
    
    // Smooth scroll to menu section
    setTimeout(() => {
        window.scrollTo({ top: menuSection.offsetTop - 100, behavior: 'smooth' });
    }, 100);

    fetch(`/location-menu/${categoryId}/${locationId}`)
        .then(response => response.json())
        .then(data => {
            menuGrid.innerHTML = '';

            if(data.length === 0){
                menuGrid.innerHTML = `
                    <div class="empty-message">
                        <i class='bx bx-food-menu'></i>
                        <p>No menu items available in this location for this category.</p>
                    </div>
                `;
            } else {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'menu-item';
                    div.innerHTML = `
                        <img src="${item.image ? '/'+item.image : '/images/placeholder.jpg'}" alt="${item.name}">
                        <div class="menu-overlay">
                            <div class="menu-top-row">
                                <div class="menu-badge">${item.category || 'SPECIAL'}</div>
                                <button type="button" class="add-to-cart" 
                                    data-food-id="${item.id}" 
                                    data-food-image="${item.image || 'images/placeholder.jpg'}" 
                                    data-food-name="${item.name}" 
                                    data-food-price="${item.price}">
                                    <i class='bx bx-plus'></i>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                            <h3>${item.name}</h3>
                            ${item.description ? `<p>${item.description}</p>` : ''}
                            <div class="price">${selectedCurrency} ${parseFloat(item.price).toFixed(2)}</div>
                        </div>
                    `;
                    menuGrid.appendChild(div);
                });
            }
        })
        .catch(error => {
            menuGrid.innerHTML = `
                <div class="empty-message">
                    <i class='bx bx-error-circle'></i>
                    <p>Error loading menu items. Please try again.</p>
                </div>
            `;
            console.error('Error:', error);
        });
}

function backToLocations() {
    const menuSection = document.getElementById('food-menu-section');
    menuSection.style.display = 'none';
    
    window.scrollTo({ 
        top: document.querySelector('.location-selection-section').offsetTop - 100, 
        behavior: 'smooth' 
    });
}
</script>

@endsection