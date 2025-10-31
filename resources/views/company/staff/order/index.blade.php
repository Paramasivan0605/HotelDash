@extends('company.staff.main')

@section('title', 'Customer Orders')

@section('content')

    <section>

        <div class="customer-order-index">

            <main>

                @if (session('success-message'))
                    <div class="success-message left-green">
                        <i class='bx bxs-check-circle'></i>
                        <div class="text">
                            <span>Success</span>
                            <span class="message">{{ session('success-message') }}</span>
                        </div>
                    </div>
                @endif

                <div class="content">

                    <div class="header">
                        <h1>Manage Customer Orders</h1>
                    </div>

                    <div class="statistic">

                        <div class="item1">
                            <i class='bx bxs-check-circle'></i>
                            <div class="data">
                                <span class="title">Total Orders Completed ({{ date('j M', strtotime(now())) }})</span>
                                <span class="data">Total of 51 Order</span>
                            </div>
                        </div>

                        <div class="item2">
                            <i class='bx bxs-info-circle'></i>
                            <div class="data">
                                <span class="title">Total Orders Pending ({{ date('j M', strtotime(now())) }})</span>
                                <span class="data">51 Still Pending</span>
                            </div>
                        </div>

                        <div class="item"></div>
                    </div>

                    <div class="bottom-section">

                        <div class="table-top">
                            <h3>Manage Orders</h3>
                            <div class="button">
                                <a href="{{ route('customer-order-create') }}" class="add"><i
                                        class='bx bxs-plus-circle'></i><span>Check Table</span></a>
                            </div>
                        </div>

                        <table>

                            <thead>
                                <tr>
                                    <th><input type="checkbox"></th>
                                    <th>Food Order</th>
                                    <th>Order Status</th>
                                    <th>Paid Status</th>
                                    <th>Payment Method</th>
                                    <th>Total Price</th>
                                    <th>Customer Contact No.</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($customerOrder as $order)
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>
                                            @foreach ($order->customerOrderDetail as $orderDetail)
                                                {{ Str::limit($orderDetail->foodMenu->name, 10) }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ $order->order_status }}</td>
                                        <td>
                                            @if ($order->isPaid)
                                                Paid
                                            @else
                                                Not Paid
                                            @endif
                                        </td>
                                        <td>{{ $order->payment_type }}</td>
                                        <td>${{ number_format($order->order_total_price, 2) }}</td>
                                        <td>{{ $order->customer_contact }}</td>
                                        <td>
                                            <button class="modal-button" 
                                                    data-id="{{ $order->id }}"
                                                    data-status="{{ $order->order_status }}"
                                                    data-food-list="{{ $order->customerOrderDetail->map(function($detail) { return $detail->foodMenu->name; })->implode(',') }}">
                                                <i class='bx bxs-pencil'></i>
                                                <span>Edit</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                        @include('company.staff.order.modal.edit')

                    </div>

                </div>

            </main>

        </div>

    </section>

@endsection