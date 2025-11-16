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
                        <p class="subtitle">Manage and track all delivery orders</p>
                    </div>
                </div>

                <!-- Enhanced Filter Section -->
                <div class="filter-section">
                    <form method="GET" action="{{ route('delivery-management') }}" class="filter-form">
                        
                        <!-- Search Bar -->
                        <div class="search-bar">
                            <i class='bx bx-search'></i>
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search by Order ID, Customer Name, or Contact..." 
                                   value="{{ $searchQuery ?? '' }}"
                                   class="search-input">
                        </div>

                        <!-- Filter Row -->
                        <div class="filters-row">
                            <!-- Staff Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-user'></i> Staff</label>
                                <select name="staff_id" class="filter-select">
                                    <option value="">All Staff</option>
                                    @foreach($staffList as $staff)
                                        <option value="{{ $staff->id }}" 
                                            {{ $selectedStaff == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-map'></i> Location</label>
                                <select name="location_id" class="filter-select">
                                    <option value="">All Locations</option>
                                    @foreach($locationList as $location)
                                        <option value="{{ $location->location_id }}" 
                                            {{ $selectedLocation == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-info-circle'></i> Status</label>
                                <select name="status" class="filter-select">
                                    <option value="">All Statuses</option>
                                    @foreach($statusList as $status)
                                        <option value="{{ $status->value }}" 
                                            {{ $selectedStatus == $status->value ? 'selected' : '' }}>
                                            {{ $status->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Status Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-money'></i> Payment</label>
                                <select name="payment_status" class="filter-select">
                                    <option value="">All Payments</option>
                                    <option value="1" {{ $selectedPaymentStatus === '1' ? 'selected' : '' }}>Paid</option>
                                    <option value="0" {{ $selectedPaymentStatus === '0' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>

                            <!-- Delivery Type Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-package'></i> Delivery Type</label>
                                <select name="delivery_type" class="filter-select">
                                    <option value="">All Types</option>
                                    @foreach($deliveryTypes as $type)
                                        <option value="{{ $type }}" 
                                            {{ $selectedDeliveryType == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date From Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-calendar'></i> From Date</label>
                                <input type="date" 
                                       name="date_from" 
                                       value="{{ $dateFrom ?? '' }}"
                                       class="filter-select">
                            </div>

                            <!-- Date To Filter -->
                            <div class="filter-group">
                                <label><i class='bx bx-calendar-check'></i> To Date</label>
                                <input type="date" 
                                       name="date_to" 
                                       value="{{ $dateTo ?? '' }}"
                                       class="filter-select">
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="btn-apply">
                                <i class='bx bx-filter-alt'></i>
                                Apply Filters
                            </button>
                            
                            @if($selectedStaff || $selectedLocation || $selectedStatus || $selectedPaymentStatus !== null || $selectedDeliveryType || $dateFrom || $dateTo || $searchQuery)
                                <a href="{{ route('delivery-management') }}" class="btn-clear">
                                    <i class='bx bx-x'></i>
                                    Clear All
                                </a>
                            @endif

                            <button type="button" class="btn-toggle" onclick="toggleFilters()">
                                <i class='bx bx-chevron-down'></i>
                                <span class="toggle-text">Hide Filters</span>
                            </button>
                        </div>
                    </form>
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
                                    <td><span class="order-id-badge">#{{ Str::substr($order->id, 0, 8) }}</span></td>
                                    <td>
                                        <div class="customer-info">
                                            <i class='bx bxs-user-circle'></i>
                                            <span>{{ $order->customer->name ?? 'Guest' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $order->customer_contact }}</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($order->delivery_type) }}">
                                            {{ $order->delivery_type }}
                                        </span>
                                    </td>
                                    <td>{{ @$order->location->location_name }}</td>
                                    <td>
                                        <span class="price-badge">
                                            {{ $order->location->currency ?? '₹' }}
                                            {{ number_format($order->order_total_price, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status badge-{{ strtolower($order->order_status->value) }}">
                                            {{ $order->order_status->value }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->assignedStaff)
                                            <div class="staff-info">
                                                <i class='bx bxs-user-badge'></i>
                                                <span>{{ $order->assignedStaff->name }}</span>
                                            </div>
                                        @else
                                            <span class="unassigned">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->isPaid)
                                            <span class="badge badge-success">
                                                <i class='bx bx-check-circle'></i> Paid
                                            </span>
                                        @else
                                            <span class="badge badge-pending">
                                                <i class='bx bx-time'></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <span class="date">{{ $order->created_at->format('d M Y') }}</span>
                                            <span class="time">{{ $order->created_at->format('h:i A') }}</span>
                                        </div>
                                    </td>
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
                                                <h3><i class='bx bx-history'></i> Order History</h3>
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
                                            @if($selectedStaff || $selectedLocation || $selectedStatus || $selectedPaymentStatus !== null || $selectedDeliveryType || $dateFrom || $dateTo || $searchQuery)
                                                <a href="{{ route('delivery-management') }}" class="btn-link">Clear all filters</a>
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
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} results
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
            margin-bottom: 24px;
        }

        .header .left h1 {
            margin: 0 0 4px 0;
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        /* Filter Section Styles */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .search-bar {
            position: relative;
            margin-bottom: 20px;
        }

        .search-bar i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 20px;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }

        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-group label i {
            font-size: 16px;
            color: #6b7280;
        }

        .filter-select {
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
            outline: none;
        }

        .filter-select:hover {
            border-color: #3b82f6;
        }

        .filter-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-apply {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-apply:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-clear {
            padding: 10px 20px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-clear:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        .btn-toggle {
            padding: 10px 16px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            margin-left: auto;
        }

        .btn-toggle:hover {
            background: #e5e7eb;
        }

        .btn-toggle i {
            transition: transform 0.3s;
        }

        .btn-toggle.collapsed i {
            transform: rotate(-90deg);
        }

        .orders-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
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

        .order-id-badge {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #3b82f6;
        }

        .customer-info, .staff-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .customer-info i, .staff-info i {
            font-size: 20px;
            color: #6b7280;
        }

        .price-badge {
            font-weight: 600;
            color: #059669;
        }

        .unassigned {
            color: #9ca3af;
            font-style: italic;
        }

        .date-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .date-info .date {
            font-weight: 500;
        }

        .date-info .time {
            font-size: 12px;
            color: #6b7280;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .badge i {
            font-size: 14px;
        }

        .badge-pickup { background: #dbeafe; color: #1e40af; }
        .badge-delivery { background: #fef3c7; color: #92400e; }
        
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-ordered { background: #fff3cd; color: #856404; }
        .badge-preparing { background: #cfe2ff; color: #084298; }
        .badge-ready { background: #d1ecf1; color: #0c5460; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .badge-delivered { background: #d4edda; color: #155724; }
        .badge-ready_to_deliver { background: #e0e7ff; color: #3730a3; }
        .badge-delivery_on_the_way { background: #fef3c7; color: #92400e; }
        .badge-success { background: #d4edda; color: #155724; }

        .btn-history {
            padding: 8px 14px;
            background: #3b82f6;
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
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
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
            display: flex;
            align-items: center;
            gap: 8px;
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
            background: #3b82f6;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #3b82f6;
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
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .btn-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        .pagination {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 12px 12px;
        }

        .pagination .count {
            font-size: 14px;
            color: #6b7280;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .filters-row {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-toggle {
                margin-left: 0;
            }

            .orders-table-container {
                overflow-x: auto;
            }

            .orders-table {
                min-width: 1000px;
            }

            .pagination {
                flex-direction: column;
                gap: 12px;
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

        function toggleFilters() {
            const filtersRow = document.querySelector('.filters-row');
            const toggleBtn = document.querySelector('.btn-toggle');
            const toggleText = toggleBtn.querySelector('.toggle-text');
            
            if (filtersRow.style.display === 'none') {
                filtersRow.style.display = 'grid';
                toggleBtn.classList.remove('collapsed');
                toggleText.textContent = 'Hide Filters';
            } else {
                filtersRow.style.display = 'none';
                toggleBtn.classList.add('collapsed');
                toggleText.textContent = 'Show Filters';
            }
        }

        // Auto-submit on Enter key in search
        document.querySelector('.search-input')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.closest('form').submit();
            }
        });
    </script>

@endsection