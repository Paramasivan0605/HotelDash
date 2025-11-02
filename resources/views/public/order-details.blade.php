@extends('main')

@section('title', 'Order Details')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="mt-4">
                <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                    ← Back to Orders
                </a>
            </div>

            <div class="row mt-2">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->customerOrderDetail as $detail)
                                        @php
                                            // Get the first food location price (you might want to store location_id in order details)
                                            $foodPrice = $detail->foodMenu->foodLocations->first()->price ?? $detail->foodMenu->price ?? 0;
                                            $itemTotal = $foodPrice * $detail->quantity;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($detail->foodMenu && $detail->foodMenu->image)
                                                        <img src="{{ asset('storage/' . $detail->foodMenu->image) }}" 
                                                             alt="{{ $detail->foodMenu->name }}" 
                                                             class="img-thumbnail me-3" 
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $detail->foodMenu->name ?? 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>RM {{ number_format($foodPrice, 2) }}</td>
                                            <td>RM {{ number_format($itemTotal, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Order ID:</strong> #{{ $order->id }}
                            </div>
                            <div class="mb-3">
                                <strong>Order Date:</strong> 
                                {{ $order->created_at->format('M d, Y h:i A') }}
                            </div>
                            <div class="mb-3">
                                <strong>Delivery Type:</strong>
                                <span class="badge bg-info">{{ $order->delivery_type }}</span>
                            </div>
                            @if($order->diningTable)
                            <div class="mb-3">
                                <strong>Table:</strong> {{ $order->diningTable->table_name }}
                            </div>
                            @endif
                            <div class="mb-3">
                                <strong>Payment Method:</strong>
                                <span class="badge bg-{{ $order->payment_type == 'cash' ? 'success' : 'primary' }}">
                                    {{ ucfirst($order->payment_type) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $order->order_status == 'completed' ? 'success' : 'warning' }}">
                                    {{ $order->order_status }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Contact:</strong> {{ $order->customer_contact }}
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Amount:</strong>
                                <span class="h5 text-primary mb-0">RM {{ number_format($order->order_total_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection