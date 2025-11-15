@extends('company.admin.main')

@section('title', 'Dashboard')

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
                        <h1>Dashboard</h1>
                    </div>
                    <div class="right">
                        <form method="GET" action="{{ route('admin-dashboard') }}" class="location-filter-form">
                            <div class="filter-group">
                                <i class='bx bx-map'></i>
                                <select name="location_id" id="locationFilter" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Locations</option>
                                    @foreach($locationList as $location)
                                        <option value="{{ $location->location_id }}" 
                                            {{ $selectedLocation == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($selectedLocation)
                                <a href="{{ route('admin-dashboard') }}" class="clear-filter-btn" title="Clear Filter">
                                    <i class='bx bx-x'></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Insight -->
                <ul class="insights">
                    <li>
                        <i class='bx bxs-user'></i>
                        <span class="info">
                            <h3>{{ @$staffcount }}</h3>
                            <p>Total Staff</p>
                        </span>
                    </li>
                    <li>
                        <i class='bx bx-show-alt'></i>
                        <span class="info">
                            <h3>{{ @$salescount }}</h3>
                            <p>Total Orders</p>
                        </span>
                    </li>
                    <li>
                        <i class='bx bx-line-chart'></i>
                        <span class="info">
                            <h3>฿ {{ number_format($totalThbAmount, 2) }}</h3>
                            <p>Total Price (THB)</p>
                        </span>
                    </li>
                    <li>
                        <i class='bx bx-line-chart'></i>
                        <span class="info">
                            <h3>Rs {{ number_format($totalLkrAmount, 2) }}</h3>
                            <p>Total Price (LKR)</p>
                        </span>
                    </li>
                </ul>

                <!-- Bottom Section -->
                <div class="bottom-section">
                    <div class="orders">
                        <div class="header">
                            <div class="header-left">
                                <i class='bx bx-receipt'></i>
                                <h3>Recent Orders</h3>
                            </div>
                        </div>

                        <div class="table-wrapper">
                            @if($recentOrders->count() > 0)
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Staff</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <span class="order-id">#{{ substr($order->id, 0, 8) }}</span>
                                                </td>
                                                <td>
                                                    <div class="user-info">
                                                        <i class='bx bxs-user-circle'></i>
                                                        <div>
                                                            <p class="name">{{ $order->customer->name ?? 'Walk-in Customer' }}</p>
                                                            @if($order->customer_contact)
                                                                <span class="contact">{{ $order->customer_contact }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($order->assignedStaff)
                                                        <div class="staff-badge">
                                                            <i class='bx bxs-user-badge'></i>
                                                            {{ $order->assignedStaff->name }}
                                                        </div>
                                                    @else
                                                        <span class="unassigned">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="amount">
                                                        {{ $order->location->currency ?? '' }} 
                                                        {{ number_format($order->order_total_price, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="date">{{ $order->created_at->format('d M Y') }}</span>
                                                    <span class="time">{{ $order->created_at->format('h:i A') }}</span>
                                                </td>
                                                <td>
                                                    <span class="status {{ strtolower($order->order_status->value) }}">
                                                        {{ $order->order_status->value }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="no-data">
                                    <i class='bx bx-inbox'></i>
                                    <p>No orders found</p>
                                    @if($selectedLocation)
                                        <a href="{{ route('admin-dashboard') }}" class="btn-link">Clear filter</a>
                                    @endif
                                </div>
                            @endif
                        </div>

                    </div>

                    <!-- Reminders -->
                    <div class="reminders">
                        <div class="header">
                            <i class='bx bx-note'></i>
                            <h3>Reminders</h3>
                            <i class='bx bx-filter'></i>
                            <i class='bx bx-plus'></i>
                        </div>

                        <ul class="task-list">
                            <li class="completed">
                                <div class="task-title">
                                    <i class='bx bx-check-circle'></i>
                                    <p>Start Our Meeting</p>
                                </div>
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </li>
                            <li class="completed">
                                <div class="task-title">
                                    <i class='bx bx-check-circle'></i>
                                    <p>Review Daily Reports</p>
                                </div>
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </li>
                            <li class="not-completed">
                                <div class="task-title">
                                    <i class='bx bx-x-circle'></i>
                                    <p>Check Inventory</p>
                                </div>
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </li>
                        </ul>
                    </div>
                    <!-- End of Reminders -->

                </div>

            </main>

        </section>

    </div>

    <style>
        /* Header Styles */
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

        /* Location Filter Form */
        .location-filter-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .location-filter-form .filter-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .location-filter-form .filter-group i {
            position: absolute;
            left: 12px;
            color: #6b7280;
            font-size: 18px;
            pointer-events: none;
            z-index: 1;
        }

        .location-filter-form .filter-select {
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

        .location-filter-form .filter-select:hover {
            border-color: #3b82f6;
        }

        .location-filter-form .filter-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .clear-filter-btn {
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

        .clear-filter-btn:hover {
            background: #fecaca;
            transform: scale(1.05);
        }

        .clear-filter-btn i {
            font-size: 20px;
        }

        /* Enhanced Orders Section */
        .orders {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            flex: 1;
        }

        .orders .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .orders .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .orders .header-left i {
            font-size: 24px;
            color: #3b82f6;
        }

        .orders .header-left h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        /* Table Styles */
        .table-wrapper {
            overflow-x: auto;
        }

        .orders table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .orders table thead {
            background: #f9fafb;
        }

        .orders table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .orders table thead th:first-child {
            border-top-left-radius: 8px;
        }

        .orders table thead th:last-child {
            border-top-right-radius: 8px;
        }

        .orders table tbody tr {
            transition: background 0.2s;
        }

        .orders table tbody tr:hover {
            background: #f9fafb;
        }

        .orders table tbody td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #374151;
        }

        /* Order ID */
        .order-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #6366f1;
            background: #eef2ff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info i {
            font-size: 32px;
            color: #3b82f6;
        }

        .user-info .name {
            font-weight: 500;
            color: #1f2937;
            margin: 0;
        }

        .user-info .contact {
            font-size: 12px;
            color: #6b7280;
        }

        /* Staff Badge */
        .staff-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .staff-badge i {
            font-size: 16px;
        }

        .unassigned {
            color: #9ca3af;
            font-style: italic;
            font-size: 13px;
        }

        /* Amount */
        .amount {
            font-weight: 600;
            color: #059669;
            font-size: 15px;
        }

        /* Date & Time */
        .date {
            display: block;
            font-weight: 500;
            color: #374151;
        }

        .time {
            display: block;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* Status Badges */
        .status {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            white-space: nowrap;
            display: inline-block;
        }

        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.delivered {
            background: #fef3c7;
            color: #92400e;
        }

        .status.delivery_on_the_way,
        .status.process {
            background: #88b3ec;
            color: #1e40af;
        }

        .status.cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status.confirmed {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status.ordered {
            background: #d1b65b;
            color: #856404;
        }

        .status.preparing {
            background: #739fe0;
            color: #084298;
        }

        .status.ready_to_deliver {
            background: #7cd2e1;
            color: #0c5460;
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 48px 24px;
            color: #9ca3af;
        }

        .no-data i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .no-data p {
            font-size: 16px;
            margin-bottom: 12px;
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

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header .right {
                width: 100%;
            }

            .location-filter-form {
                width: 100%;
            }

            .location-filter-form .filter-select {
                width: 100%;
            }

            .orders .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .orders table {
                font-size: 12px;
            }

            .orders table thead th,
            .orders table tbody td {
                padding: 8px;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .user-info i {
                display: none;
            }
        }

        /* Animation for table rows */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .orders table tbody tr {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

@endsection