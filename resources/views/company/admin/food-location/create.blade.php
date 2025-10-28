@extends('company.admin.main')

@section('title', 'Create')

@section('content')

    <div class="food-menu-create">

        <section>

            <main>

                @if (session('success-message'))
                    <div class="success-message">
                        <i class='bx bxs-check-circle'></i>
                        <div class="text">
                            <span>Success</span>
                            <span class="message">{{ session('success-message') }}</span>
                        </div>
                    </div>
                @endif

                <div class="header">
                    <div class="left">
                        <h1>Create Food Location </h1>
                    </div>
                </div>

                <div class="top-section">

                    <form action="{{ route('food-location.store') }}" method="POST" enctype="multipart/form-data" id="imageForm">
                        @csrf

                        <div class="form-add-menu">

                            <div class="header">
                                <h4>Food Location Details</h4>
                            </div>

                            <span class="star">Food name</span>
                            <div class="dropdown">
                                <div class="select">
                                    <span class="selected">Select Menu</span>
                                    <div class="caret"><i class='bx bx-chevron-down'></i></div>
                                </div>
                                <ul class="menu">
                                    @foreach ($foodMenu as $menu)
                                        <li data-value="{{ $menu->id }}">{{ $menu->name }}</li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="food_id" value="" required>
                            </div>

                            <span class="star">Location</span>
                            <div class="dropdown">
                                <div class="select">
                                    <span class="selected">Select Location</span>
                                    <div class="caret"><i class='bx bx-chevron-down'></i></div>
                                </div>
                                <ul class="menu">
                                    @foreach ($locations as $location)
                                        <li data-value="{{ $location->id }}">{{ $location->location_name }}</li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="location_id" value="" required>
                            </div>

                            <span class="star">Price</span>
                            <input type="text" name="price" placeholder="Enter food price" value="{{ old('price') }}"
                                required>

                        </div>

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        
                            <div class="button">
                                <input type="submit" value="Add Food Location">
                                <a href="{{ route('food-location') }}"><span>Cancel</span></a>
                            </div>
                    </form>
                </div>

            </main>

        </section>

    </div>

@endsection
