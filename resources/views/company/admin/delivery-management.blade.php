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
                </div>

                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Delivery Type</th>
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
                                    <td>₹{{ number_format($order->order_total_price, 2) }}</td>
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
                                    <td colspan="10">
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
                                    <td colspan="10" class="no-data">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pagination-container">
                        {{ $orders->links() }}
                    </div>
                </div>

            </main>

        </section>

    </div>

    <style>
        .orders-table-container {
            background: white;
            border-radius: 8px;
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
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #495057;
        }

        .orders-table tbody tr.order-row {
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.2s;
        }

        .orders-table tbody tr.order-row:hover {
            background-color: #f8f9fa;
        }

        .orders-table td {
            padding: 12px 16px;
            font-size: 14px;
            color: #212529;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
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
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background 0.2s;
        }

        .btn-history:hover {
            background: #0056b3;
        }

        .history-row {
            background: #f8f9fa;
        }

        .history-container {
            padding: 20px;
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
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .history-header h3 {
            margin: 0;
            color: #212529;
            font-size: 18px;
        }

        .btn-close {
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-close:hover {
            background: #dee2e6;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
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
            bottom: -20px;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
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
            padding: 40px;
            color: #6c757d;
        }

        .pagination-container {
            padding: 20px;
            display: flex;
            justify-content: center;
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