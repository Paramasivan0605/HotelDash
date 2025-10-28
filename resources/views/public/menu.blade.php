@extends('main')

@section('title', 'Menu')

@section('content')

    <div class="menu-page">

        <section>

            <main>

                <div class="page">

                    @if ($menu->isEmpty())

                        <div class="container-empty">
                            <i class='bx bxs-error-alt'></i>
                            <div class="text">
                                <span class="top">Sorry, no menu available at the moment.</span>
                                <span class="bottom">Please check back later!</span>
                            </div>
                        </div>
                    @else
                        <div class="category-banner">
                            <h1>Our Signature Menu</h1>
                        </div>

                        <div class="signature-menu-section">
                            <div class="menu-grid">
                                @foreach($category as $index => $item)
                                    <a href="{{ route('locationmenu', ['categoryId' => $item->id]) }}">
                                        <div class="menu-item">
                                            <img src="{{ asset($item->image ?? 'images/placeholder.jpg') }}" alt="{{ $item->name }}">
                                            <div class="menu-overlay">
                                                <div class="menu-badge">{{ strtoupper($item->category ?? 'SPECIAL') }}</div>
                                                <h3>{{ $item->name }}</h3>
                                                @if(isset($item->price))
                                                <div class="price">RM {{ number_format($item->price, 2) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                    @endif

                </div>

            </main>

            @include('public.modal.success-message')

        </section>

    </div>

    <style>
        .signature-menu-section {
            padding: 20px 20px;
            max-width: 1400px;
            margin: 0 auto;
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
            bottom: 0;
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

        .menu-badge {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 10px;
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

        @media (max-width: 768px) {
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
        }

        @media (max-width: 480px) {
            .signature-menu-section {
                padding: 40px 15px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

@endsection