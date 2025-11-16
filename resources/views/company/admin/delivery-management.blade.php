@extends('company.admin.main')

@section('title', 'Delivery Management')

@section('content')

    <div class="dashboard">

        <section>

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

                <div class="header">
                    <div class="left">
                        <h1>Delivery Management</h1>
                    </div>
                    <div class="right">
                        <form method="GET" action="{{ route('delivery-management') }}" class="filter-form">
                            <div class="filter-group">
                                <i class='bx bx-user'></i>
                                <select name="staff_id" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Staff</option>
                                    @foreach($staffList as $staff)
                                        <option value="{{ $staff->id }}" 
                                            {{ $selectedStaff == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <i class='bx bx-map'></i>
                                <select name="location_id" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Locations</option>
                                    @foreach($locationList as $location)
                                        <option value="{{ $location->location_id }}" 
                                            {{ $selectedLocation == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($selectedStaff || $selectedLocation)
                                <a href="{{ route('delivery-management') }}" class="clear-filters" title="Clear Filters">
                                    <i class='bx bx-x'></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Delivery Type</th>
                                <th>Location</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Assigned Staff</th>
                                <th>Payment</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="order-row" data-order-id="{{ $order->id }}">
                                    <td>{{ Str::substr($order->id, 0, 8) }}...</td>
                                    <td>{{ $order->customer->name ?? 'Guest' }}</td>
                                    <td>{{ $order->customer_contact }}</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($order->delivery_type) }}">
                                            {{ $order->delivery_type }}
                                        </span>
                                    </td>
                                    <td>{{ @$order->location->location_name }} </td>
                                    <td>{{ $order->location->currency ?? '₹' }}
                                       {{ number_format($order->order_total_price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-status badge-{{ strtolower($order->order_status->value) }}">
                                            {{ $order->order_status->value }}
                                        </span>
                                    </td>
                                    <td>{{ $order->assignedStaff->name ?? 'Unassigned' }}</td>
                                    <td>
                                        @if($order->isPaid)
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <button class="btn-history" onclick="toggleHistory('{{ $order->id }}')">
                                            <i class='bx bx-history'></i> History
                                        </button>
                                    </td>
                                </tr>
                                <tr class="history-row" id="history-{{ $order->id }}" style="display: none;">
                                    <td colspan="11">
                                        <div class="history-container">
                                            <div class="history-header">
                                                <h3>Order History</h3>
                                                <button class="btn-close" onclick="toggleHistory('{{ $order->id }}')">
                                                    <i class='bx bx-x'></i>
                                                </button>
                                            </div>
                                            <div class="history-content">
                                                @if($order->histories->count() > 0)
                                                    <div class="timeline">
                                                        @foreach($order->histories->sortByDesc('created_at') as $history)
                                                            <div class="timeline-item">
                                                                <div class="timeline-marker"></div>
                                                                <div class="timeline-content">
                                                                    <div class="timeline-header">
                                                                        <span class="timeline-action">{{ $history->action }}</span>
                                                                        <span class="timeline-date">{{ $history->created_at->format('d M Y, h:i A') }}</span>
                                                                    </div>
                                                                    <div class="timeline-body">
                                                                        <p><strong>Staff:</strong> {{ $history->staff->name ?? 'System' }}</p>
                                                                        @if($history->old_status && $history->new_status)
                                                                            <p>
                                                                                <strong>Status Change:</strong> 
                                                                                <span class="badge badge-{{ strtolower($history->old_status) }}">{{ $history->old_status }}</span>
                                                                                <i class='bx bx-right-arrow-alt'></i>
                                                                                <span class="badge badge-{{ strtolower($history->new_status) }}">{{ $history->new_status }}</span>
                                                                            </p>
                                                                        @endif
                                                                        @if($history->notes)
                                                                            <p><strong>Notes:</strong> {{ $history->notes }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="no-history">No history available for this order.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="no-data">
                                        <div class="no-data-content">
                                            <i class='bx bx-inbox'></i>
                                            <p>No orders found</p>
                                            @if($selectedStaff || $selectedLocation)
                                                <a href="{{ route('delivery-management') }}" class="btn-link">Clear filters</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
                    <div class="pagination">
                        <div class="count">
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} out of {{ $orders->total() }} results
                        </div>

                        <div class="pagination-number">
                            <div class="page-number">
                                {{ $orders->appends(request()->query())->render('partials.paginator') }}
                            </div>
                        </div>
                    </div>
            </main>

        </section>

    </div>

    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header .left h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
        }

        .header .right {
            display: flex;
            align-items: center;
        }

        /* Filter Form Styles */
        .filter-form {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .filter-group i {
            position: absolute;
            left: 12px;
            color: #6b7280;
            font-size: 18px;
            pointer-events: none;
            z-index: 1;
        }

        .filter-select {
            padding: 10px 16px 10px 38px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 180px;
            outline: none;
        }

        .filter-select:hover {
            border-color: #3b82f6;
        }

        .filter-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .clear-filters {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #fee2e2;
            color: #dc2626;
            transition: all 0.2s;
            text-decoration: none;
        }

        .clear-filters:hover {
            background: #fecaca;
            transform: scale(1.05);
        }

        .clear-filters i {
            font-size: 22px;
        }

        .orders-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .orders-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .orders-table tbody tr.order-row {
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.2s;
        }

        .orders-table tbody tr.order-row:hover {
            background-color: #f8f9fa;
        }

        .orders-table td {
            padding: 14px 16px;
            font-size: 14px;
            color: #212529;
            vertical-align: middle;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-delivered { background: #e3f2fd; color: #1976d2; }
        .badge-ready_to_deliver { background: #f3e5f5; color: #7b1fa2; }
        .badge-delivery_on_the_way { background: #fff3e0; color: #f57c00; }

        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-ordered { background: #fff3cd; color: #856404; }
        .badge-preparing { background: #cfe2ff; color: #084298; }
        .badge-ready { background: #d1ecf1; color: #0c5460; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .badge-success { background: #d4edda; color: #155724; }

        .btn-history {
            padding: 8px 14px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-history:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,123,255,0.3);
        }

        .history-row {
            background: #f8f9fa;
        }

        .history-container {
            padding: 24px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #dee2e6;
        }

        .history-header h3 {
            margin: 0;
            color: #212529;
            font-size: 18px;
            font-weight: 600;
        }

        .btn-close {
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-close:hover {
            background: #dee2e6;
            color: #212529;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 0;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #007bff;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 12px;
            bottom: -24px;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-content {
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .timeline-action {
            font-weight: 600;
            color: #212529;
            font-size: 15px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
        }

        .timeline-body p {
            margin: 8px 0;
            font-size: 14px;
            color: #495057;
        }

        .timeline-body p:last-child {
            margin-bottom: 0;
        }

        .timeline-body .bx-right-arrow-alt {
            margin: 0 8px;
            color: #6c757d;
        }

        .no-history {
            text-align: center;
            color: #6c757d;
            padding: 40px;
            font-size: 14px;
        }

        .no-data {
            text-align: center;
            padding: 0;
        }

        .no-data-content {
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-data-content i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .no-data-content p {
            font-size: 16px;
            margin: 12px 0;
            color: #495057;
        }

        .btn-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .btn-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .pagination-container {
            padding: 20px;
            display: flex;
            justify-content: center;
            border-top: 1px solid #dee2e6;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header .right {
                width: 100%;
            }

            .filter-form {
                flex-direction: column;
                width: 100%;
            }

            .filter-group {
                width: 100%;
            }

            .filter-select {
                width: 100%;
            }

            .orders-table-container {
                overflow-x: auto;
            }

            .orders-table {
                min-width: 1000px;
            }
        }
    </style>

    <script>
        function toggleHistory(orderId) {
            const historyRow = document.getElementById('history-' + orderId);
            
            if (historyRow.style.display === 'none') {
                // Hide all other open history rows
                document.querySelectorAll('.history-row').forEach(row => {
                    row.style.display = 'none';
                });
                // Show this history row
                historyRow.style.display = 'table-row';
            } else {
                historyRow.style.display = 'none';
            }
        }
    </script>

@endsection