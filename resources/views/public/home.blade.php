@extends('main')

@section('title', 'Home')

@section('content')

<div class="position-relative" style="min-height: 100vh;">
    <!-- Hero Section with Background -->
    <section class="position-relative overflow-hidden" 
             style="background: linear-gradient(to bottom, rgba(54, 57, 73, 0.7), rgba(54, 57, 73, 0.7)), 
                    url('{{ asset('images/home-bg.jpg') }}') center/cover no-repeat;
                    min-height: calc(100vh - 70px);">
        
        <div class="container">
            <div class="row align-items-center" style="min-height: calc(100vh - 70px);">
                <div class="col-lg-8 col-xl-7">
                    <!-- Title -->
                    <div class="text-white mb-4" data-aos="fade-up">
                        <h1 class="display-1 fw-bold mb-3" style="font-size: clamp(2.5rem, 8vw, 5rem); line-height: 1.1;">
                            Welcome To
                        </h1>
                        <h1 class="display-1 fw-bold" style="font-size: clamp(2.5rem, 8vw, 5rem); line-height: 1.1;">
                            Hash Restaurant
                        </h1>
                    </div>

                    <!-- Description -->
                    <div class="text-white mb-4" data-aos="fade-up" data-aos-delay="200">
                        <p class="lead mb-2" style="font-size: clamp(0.9rem, 2vw, 1.1rem); line-height: 1.8;">
                            Embark on a transcendent culinary odyssey at our esteemed venue, where the magic of
                            Malaysian cuisine is elevated by our local maestro boasting two Michelin stars.
                        </p>
                        <p class="lead mb-2" style="font-size: clamp(0.9rem, 2vw, 1.1rem); line-height: 1.8;">
                            Gather your loved ones for an unforgettable gastronomic journey through the enchanting 
                            world of flavor we have meticulously crafted for you.
                        </p>
                    </div>

                    <!-- CTA Button -->
                    <div data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('location.menu', ['id' => session('location_id')]) }}" 
                           class="btn btn-lg px-5 py-3 text-white fw-bold shadow-lg"
                           style="background: linear-gradient(135deg, #6A0DAD 0%, #A46AEF 100%); 
                                  border-radius: 50px; 
                                  transition: transform 0.3s ease, box-shadow 0.3s ease;"
                           onmouseover="this.style.transform='translateY(-3px) scale(1.05)'; this.style.boxShadow='0 10px 30px rgba(106, 13, 173, 0.5)'"
                           onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.3)'">
                            <i class="bi bi-book me-2"></i>See Our Menu
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="position-absolute bottom-0 start-0 w-100 d-none d-md-block" 
             style="height: 150px; background: linear-gradient(to top, rgba(0,0,0,0.3), transparent);"></div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 hover-card" 
                         style="transition: transform 0.3s ease;"
                         onmouseover="this.style.transform='translateY(-10px)'"
                         onmouseout="this.style.transform='translateY(0)'">
                        <div class="card-body">
                            <i class="bi bi-award text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title fw-bold mb-3">Michelin Excellence</h5>
                            <p class="card-text text-muted">Two Michelin stars awarded for exceptional culinary craftsmanship</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 hover-card"
                         style="transition: transform 0.3s ease;"
                         onmouseover="this.style.transform='translateY(-10px)'"
                         onmouseout="this.style.transform='translateY(0)'">
                        <div class="card-body">
                            <i class="bi bi-egg-fried text-warning mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title fw-bold mb-3">Authentic Malaysian</h5>
                            <p class="card-text text-muted">Traditional flavors with a modern culinary twist</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 hover-card"
                         style="transition: transform 0.3s ease;"
                         onmouseover="this.style.transform='translateY(-10px)'"
                         onmouseout="this.style.transform='translateY(0)'">
                        <div class="card-body">
                            <i class="bi bi-people text-success mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title fw-bold mb-3">Family Friendly</h5>
                            <p class="card-text text-muted">Perfect ambiance for memorable family gatherings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions (Mobile-Friendly) -->
    <section class="py-5 bg-white d-md-none">
        <div class="container">
            <h3 class="text-center fw-bold mb-4">Quick Actions</h3>
            <div class="row g-3">
                <div class="col-6">
                    <a href="{{ route('location.menu', ['id' => session('location_id')]) }}" class="btn btn-outline-primary w-100 py-3 rounded-3">
                        <i class="bi bi-book fs-3 d-block mb-2"></i>
                        <small>Menu</small>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('promotion') }}" class="btn btn-outline-warning w-100 py-3 rounded-3">
                        <i class="bi bi-tag fs-3 d-block mb-2"></i>
                        <small>Offers</small>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('reservation') }}" class="btn btn-outline-success w-100 py-3 rounded-3">
                        <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                        <small>Reserve</small>
                    </a>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-info w-100 py-3 rounded-3" id="mobile-cart-btn">
                        <i class="bi bi-cart fs-3 d-block mb-2"></i>
                        <small>Cart</small>
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- AOS Animation Library (Optional Enhancement) -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Mobile cart button
    document.getElementById('mobile-cart-btn')?.addEventListener('click', function() {
        const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
        cartOffcanvas.show();
    });
</script>

@endsection