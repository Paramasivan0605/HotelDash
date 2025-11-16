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

                @if (session('error-message'))
                    <div class="error-message left-red">
                        <i class='bx bxs-error-circle'></i>
                        <div class="text">
                            <span>Error</span>
                            <span class="message">{{ session('error-message') }}</span>
                        </div>
                    </div>
                @endif

                <div class="content">
                    <div class="header">
                        <h1>Manage Customer Orders</h1>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">My Assigned Orders</p>
                                        <h3 class="fw-bold text-primary mb-0">{{ @$myAssignedCount }}</h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-person-check text-primary fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Available Orders</p>
                                        <h3 class="fw-bold text-success mb-0">{{ @$availableCount }}</h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-cart-plus text-success fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Total Orders</p>
                                        <h3 class="fw-bold text-info mb-0">{{ @$totalCount }}</h3>
                                    </div>
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-list-ul text-info fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-semibold">
                                    <i class="bi bi-funnel"></i> Filters
                                </h5>
                                <button class="btn btn-sm btn-outline-secondary" id="clearFilters">
                                    <i class="bi bi-x-circle"></i> Clear All
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Search Input -->
                                <div class="col-md-3">
                                    <label class="form-label small">Search</label>
                                    <input type="text" class="form-control form-control-sm" id="searchInput" 
                                           placeholder="Order ID, Food, Contact...">
                                </div>

                                <!-- Order Status Filter -->
                                <div class="col-md-3">
                                    <label class="form-label small">Order Status</label>
                                    <select class="form-select form-select-sm" id="statusFilter">
                                        <option value="">All Statuses</option>
                                        <option value="ordered">Ordered</option>
                                        <option value="preparing">Preparing</option>
                                        <option value="ready_to_deliver">Ready to Deliver</option>
                                        <option value="delivery_on_the_way">Delivery on the Way</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <!-- Payment Status Filter -->
                                <div class="col-md-2">
                                    <label class="form-label small">Payment</label>
                                    <select class="form-select form-select-sm" id="paymentFilter">
                                        <option value="">All</option>
                                        <option value="paid">Paid</option>
                                        <option value="not_paid">Not Paid</option>
                                    </select>
                                </div>

                                <!-- Assignment Filter -->
                                <div class="col-md-2">
                                    <label class="form-label small">Assignment</label>
                                    <select class="form-select form-select-sm" id="assignmentFilter">
                                        <option value="">All</option>
                                        <option value="my_orders">My Orders</option>
                                        <option value="available">Available</option>
                                        <option value="others">Assigned to Others</option>
                                    </select>
                                </div>

                                <!-- Date Filter -->
                                <div class="col-md-2">
                                    <label class="form-label small">Date Range</label>
                                    <select class="form-select form-select-sm" id="dateFilter">
                                        <option value="">All Time</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Active Filters Display -->
                            <div class="mt-3" id="activeFilters" style="display: none;">
                                <small class="text-muted">Active filters:</small>
                                <div class="d-flex flex-wrap gap-2 mt-2" id="filterBadges"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-semibold">
                                    All Orders <span class="badge bg-primary" id="resultCount">{{ count($customerOrder) }}</span>
                                </h5>
                                <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <div class="bottom-section">
                            <div class="table-top">
                                <h3>Manage Orders</h3>
                            </div>

                            <table id="ordersTable">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Food Order</th>
                                        <th>Assigned To</th>
                                        <th>Order Status</th>
                                        <th>Paid Status</th>
                                        <th>Total Price</th>
                                        <th>Customer Contact</th>
                                        <th>Actions</th>
                                        <th>History</th>
                                        <th>Ordered At</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($customerOrder as $order)
                                        <tr data-order-id="{{ $order->id }}"
                                            data-status="{{ $order->order_status }}"
                                            data-payment="{{ $order->isPaid ? 'paid' : 'not_paid' }}"
                                            data-assignment="{{ !$order->assigned_staff_id ? 'available' : ($order->assigned_staff_id == $currentStaffId ? 'my_orders' : 'others') }}"
                                            data-date="{{ $order->created_at->format('Y-m-d') }}"
                                            data-search-text="{{ strtolower($order->id . ' ' . $order->customer_contact . ' ' . $order->customer->mobile ?? '') }}">
                                            <td>
                                                <span title="{{ $order->id }}">#{{ Str::limit($order->id, 8) }}</span>
                                            </td>
                                            <td data-food-items="{{ strtolower(collect($order->customerOrderDetail)->pluck('foodMenu.name')->implode(' ')) }}">
                                                @foreach ($order->customerOrderDetail as $orderDetail)
                                                    {{ Str::limit($orderDetail->foodMenu->name, 10) }}
                                                    @if (!$loop->last), @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($order->assigned_staff_id)
                                                    @if($order->assigned_staff_id == $currentStaffId)
                                                        <span class="assigned-to-you">You</span>
                                                    @else
                                                        <span class="assigned-to-other">{{ $order->assignedStaff->name ?? 'Unknown' }}</span>
                                                    @endif
                                                @else
                                                    <span class="unassigned">Available</span>
                                                @endif
                                            </td>
                                            <td class="order-status-cell" data-order-id="{{ $order->id }}" data-update-url="{{ route('update-order', $order->id) }}">
                                                <div class="status-badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}" 
                                                     data-status="{{ $order->order_status }}"
                                                     data-current-status="{{ $order->order_status }}">
                                                    {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                                                </div>
                                            </td>
                                            <td class="payment-status-cell" data-order-id="{{ $order->id }}" data-update-payment-url="{{ route('update-order-payment', $order->id) }}">
                                                <div class="payment-badge {{ $order->isPaid ? 'payment-paid' : 'payment-not-paid' }}" 
                                                     data-is-paid="{{ $order->isPaid }}"
                                                     data-current-paid="{{ $order->isPaid }}">
                                                    {{ $order->isPaid ? 'Paid' : 'Not Paid' }}
                                                </div>
                                            </td>
                                            <td>{{ $order->location->currency ?? '₹' }} {{ number_format($order->order_total_price, 2) }}</td>
                                            <td>
                                                {{ $order->customer_contact ?? 'N/A' }}
                                                @if(!empty($order->customer->mobile))
                                                    / {{ $order->customer->mobile }}
                                                @endif
                                            </td>
                                            <td class="action-buttons">
                                                @if(!$order->assigned_staff_id)
                                                    <form action="{{ route('order.accept', $order->id) }}" method="POST" class="inline-form">
                                                        @csrf
                                                        <button type="submit" class="btn-accept">Accept Order</button>
                                                    </form>
                                                @elseif($order->assigned_staff_id == $currentStaffId)
                                                    <form action="{{ route('order.unaccept', $order->id) }}" method="POST" class="inline-form">
                                                        @csrf
                                                        <button type="submit" class="btn-unaccept">Unaccept</button>
                                                    </form>
                                                @else
                                                    <span class="taken">Taken by others</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn-history" onclick="showOrderHistory('{{ $order->id }}')">
                                                    <i class='bx bx-time'></i>
                                                </button>
                                            </td>
                                            <td>{{ $order->created_at->format('j M Y, g:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- No Results Message -->
                            <div id="noResults" style="display: none; text-align: center; padding: 40px; color: #666;">
                                <i class="bi bi-search" style="font-size: 48px; opacity: 0.3;"></i>
                                <p style="margin-top: 15px; font-size: 16px;">No orders found matching your filters</p>
                                <button class="btn btn-sm btn-primary mt-2" onclick="document.getElementById('clearFilters').click()">
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- All your existing modals (Status, Payment, History) remain the same -->
        <!-- Status Change Modal -->
        <div class="status-modal" id="statusModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Change Order Status</h3>
                    <span class="close-modal">&times;</span>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Current status: <span id="currentStatus" class="current-status-badge"></span></p>
                        <div class="status-options">
                            <div class="status-option" data-status="ordered">
                                <span class="status-indicator status-ordered"></span>
                                Ordered
                            </div>
                            <div class="status-option" data-status="preparing">
                                <span class="status-indicator status-preparing"></span>
                                Preparing
                            </div>
                            <div class="status-option" data-status="ready_to_deliver">
                                <span class="status-indicator status-ready"></span>
                                Ready to Deliver
                            </div>
                            <div class="status-option" data-status="delivery_on_the_way">
                                <span class="status-indicator status-delivery"></span>
                                Delivery on the Way
                            </div>
                            <div class="status-option" data-status="delivered">
                                <span class="status-indicator status-delivered"></span>
                                Delivered
                            </div>
                            <div class="status-option" data-status="completed">
                                <span class="status-indicator status-completed"></span>
                                Completed
                            </div>
                            <div class="status-option" data-status="cancelled">
                                <span class="status-indicator status-cancelled"></span>
                                Cancelled
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cancel-btn">Cancel</button>
                        <button type="submit" class="confirm-btn" disabled>Confirm Change</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Status Change Modal -->
        <div class="payment-modal" id="paymentModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Update Payment Status</h3>
                    <span class="close-payment-modal">&times;</span>
                </div>
                <form id="paymentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Current payment status: <span id="currentPaymentStatus" class="current-payment-badge"></span></p>
                        <div class="payment-options">
                            <div class="payment-option" data-is-paid="1">
                                <span class="payment-indicator payment-paid"></span>
                                Paid
                            </div>
                            <div class="payment-option" data-is-paid="0">
                                <span class="payment-indicator payment-not-paid"></span>
                                Not Paid
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cancel-payment-btn">Cancel</button>
                        <button type="submit" class="confirm-payment-btn" disabled>Confirm Change</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Modal -->
        <div class="history-modal" id="historyModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Order History - #<span id="historyOrderId"></span></h3>
                    <span class="close-history-modal">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="timeline" id="historyTimeline">
                        <!-- Timeline content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close-history-btn">Close</button>
                </div>
            </div>
        </div>

    </section>

<style>
    /* All your existing styles remain the same */
    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        min-width: 120px;
        border: 1px solid transparent;
        color: inherit !important;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .status-ordered {
        background-color: #e3f2fd;
        color: #1976d2 !important;
        border-color: #bbdefb;
    }

    .status-preparing {
        background-color: #fff3e0;
        color: #f57c00 !important;
        border-color: #ffe0b2;
    }

    .status-ready {
        background-color: #e8f5e8;
        color: #388e3c !important;
        border-color: #c8e6c9;
    }

    .status-delivery {
        background-color: #e3f2fd;
        color: #0288d1 !important;
        border-color: #b3e5fc;
    }

    .status-delivered {
        background-color: #e8f5e8;
        color: #2e7d32 !important;
        border-color: #a5d6a7;
    }

    .status-completed {
        background-color: #e8f5e8;
        color: #1b5e20 !important;
        border-color: #81c784;
    }

    .status-cancelled {
        background-color: #ffebee;
        color: #c62828 !important;
        border-color: #ffcdd2;
    }

    .payment-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        min-width: 80px;
        border: 1px solid transparent;
        color: inherit !important;
    }

    .payment-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .payment-paid {
        background-color: #e8f5e8;
        color: #2e7d32 !important;
        border-color: #a5d6a7;
    }

    .payment-not-paid {
        background-color: #ffebee;
        color: #c62828 !important;
        border-color: #ffcdd2;
    }

    .assigned-to-you {
        background-color: #4caf50;
        color: white !important;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .assigned-to-other {
        background-color: #ff9800;
        color: white !important;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .unassigned {
        background-color: #2196f3;
        color: white !important;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .inline-form {
        margin: 0;
        display: inline;
    }

    .btn-accept, .btn-unaccept {
        padding: 6px 12px !important;
        border: none !important;
        border-radius: 4px !important;
        font-size: 12px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        font-weight: 500 !important;
        color: white !important;
        text-decoration: none !important;
        display: inline-block !important;
        text-align: center !important;
        min-width: 80px !important;
        -webkit-text-fill-color: white !important;
    }

    .btn-accept {
        background-color: #4caf50 !important;
    }

    .btn-accept:hover {
        background-color: #45a049 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .btn-unaccept {
        background-color: #f44336 !important;
    }

    .btn-unaccept:hover {
        background-color: #da190b !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .taken {
        color: #666 !important;
        font-style: italic;
        font-size: 12px;
        padding: 4px 8px;
    }

    .btn-history {
        background: none !important;
        border: none !important;
        color: #666 !important;
        cursor: pointer !important;
        font-size: 18px !important;
        padding: 4px !important;
        border-radius: 4px !important;
        transition: color 0.3s !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .btn-history:hover {
        color: #2196f3 !important;
        background-color: rgba(33, 150, 243, 0.1) !important;
    }

    /* Filter Badge Styles */
    .filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background-color: #e3f2fd;
        color: #1976d2;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .filter-badge i {
        cursor: pointer;
        font-size: 14px;
    }

    .filter-badge i:hover {
        color: #c62828;
    }

    /* Modal Styles - keeping all existing modal styles */
    .status-modal, .payment-modal, .history-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .status-modal.active, .payment-modal.active, .history-modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        overflow: hidden;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
        font-size: 18px;
        font-weight: 600;
    }

    .close-modal, .close-payment-modal, .close-history-modal {
        font-size: 24px;
        cursor: pointer;
        color: #666;
        line-height: 1;
        transition: color 0.3s;
        background: none;
        border: none;
        padding: 0;
    }

    .close-modal:hover, .close-payment-modal:hover, .close-history-modal:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
        max-height: 60vh;
        overflow-y: auto;
    }

    .modal-body p {
        margin-bottom: 15px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .current-status-badge, .current-payment-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 15px;
    }

    .payment-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 15px;
    }

    .status-option, .payment-option {
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        text-transform: capitalize;
        display: flex;
        align-items: center;
        gap: 8px;
        background: white;
        color: #333;
    }

    .status-option:hover, .payment-option:hover {
        border-color: #2196f3;
        background-color: #f5f9ff;
        color: #1976d2;
    }

    .status-option.selected, .payment-option.selected {
        border-color: #2196f3;
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .status-indicator, .payment-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background-color: #f8f9fa;
    }

    .cancel-btn, .confirm-btn, .cancel-payment-btn, .confirm-payment-btn, .close-history-btn {
        padding: 10px 20px !important;
        border: none !important;
        border-radius: 6px !important;
        cursor: pointer !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        font-size: 14px !important;
        color: inherit !important;
        text-decoration: none !important;
        display: inline-block !important;
    }

    .cancel-btn, .cancel-payment-btn, .close-history-btn {
        background-color: #f5f5f5 !important;
        color: #666 !important;
    }

    .cancel-btn:hover, .cancel-payment-btn:hover, .close-history-btn:hover {
        background-color: #e0e0e0 !important;
        color: #333 !important;
    }

    .confirm-btn, .confirm-payment-btn {
        background-color: #4caf50 !important;
        color: white !important;
    }

    .confirm-btn:disabled, .confirm-payment-btn:disabled {
        background-color: #ccc !important;
        cursor: not-allowed !important;
        color: #666 !important;
    }

    .confirm-btn:not(:disabled):hover, .confirm-payment-btn:not(:disabled):hover {
        background-color: #45a049 !important;
        color: white !important;
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #2196f3;
        z-index: 2;
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: 5px;
        top: 17px;
        width: 2px;
        height: calc(100% + 3px);
        background-color: #e0e0e0;
        z-index: 1;
    }

    .timeline-item:last-child:after {
        display: none;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border-left: 3px solid #2196f3;
    }

    .timeline-action {
        font-weight: 600;
        color: #333;
        text-transform: capitalize;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .timeline-staff {
        color: #666;
        font-size: 12px;
        margin-bottom: 2px;
    }

    .timeline-time {
        color: #999;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .timeline-status-change {
        color: #2196f3;
        font-size: 12px;
        margin: 5px 0;
        font-weight: 500;
    }

    .timeline-notes {
        color: #666;
        font-size: 12px;
        margin-top: 5px;
        font-style: italic;
        background: rgba(0,0,0,0.05);
        padding: 4px 8px;
        border-radius: 4px;
    }

    /* Table Styles */
    .customer-order-index table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .customer-order-index thead {
        background-color: #f8f9fa;
    }

    .customer-order-index th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #e9ecef;
        font-size: 14px;
    }

    .customer-order-index td {
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
        vertical-align: middle;
    }

    .customer-order-index tbody tr:hover {
        background-color: #f8f9fa;
    }

    .customer-order-index tbody tr:last-child td {
        border-bottom: none;
    }

    /* Header Styles */
    .customer-order-index .header {
        margin-bottom: 30px;
    }

    .customer-order-index .header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    /* Table Top Section */
    .table-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .table-top h3 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    /* Success and Error Messages */
    .success-message {
        background: #e8f5e8;
        border: 1px solid #4caf50;
        color: #2e7d32;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .error-message {
        background: #ffebee;
        border: 1px solid #f44336;
        color: #c62828;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .success-message i, .error-message i {
        font-size: 20px;
        flex-shrink: 0;
    }

    .success-message .text, .error-message .text {
        display: flex;
        flex-direction: column;
    }

    .success-message .text span:first-child,
    .error-message .text span:first-child {
        font-weight: 600;
        font-size: 14px;
    }

    .success-message .text .message,
    .error-message .text .message {
        font-size: 13px;
        opacity: 0.9;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .customer-order-index table {
            display: block;
            overflow-x: auto;
        }
        
        .status-options,
        .payment-options {
            grid-template-columns: 1fr;
        }
        
        .modal-content {
            width: 95%;
            margin: 20px;
        }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .customer-order-index {
            padding: 10px 5px;
        }

        .content {
            margin: 0;
            padding: 0;
        }

        .header h1 {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Statistic Cards - Mobile */
        .row.g-3.mb-4 {
            margin: 0 -5px 20px -5px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .col-md-4 {
            padding: 0 5px;
            margin-bottom: 0;
        }

        .border.rounded.p-3.bg-white.shadow-sm {
            padding: 15px !important;
        }

        .d-flex.justify-content-between.align-items-center h3 {
            font-size: 1.5rem;
        }

        /* Filter Section - Mobile */
        .card.border-0.shadow-sm.mb-4 {
            margin-bottom: 15px !important;
        }

        .card-header.bg-white.py-3 {
            padding: 12px !important;
        }

        .card-header .d-flex {
            flex-direction: row !important;
            gap: 8px;
        }

        .card-title.mb-0.fw-semibold {
            font-size: 0.95rem;
        }

        .btn.btn-sm.btn-outline-secondary {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        .card-body .row.g-3 {
            gap: 12px !important;
        }

        .col-md-3, .col-md-2 {
            width: 100%;
            padding: 0;
        }

        .form-label.small {
            font-size: 0.8rem;
            margin-bottom: 4px;
        }

        .form-control.form-control-sm,
        .form-select.form-select-sm {
            font-size: 0.85rem;
            padding: 6px 10px;
        }

        /* Active Filters - Mobile */
        #activeFilters {
            margin-top: 12px !important;
        }

        .filter-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
        }

        /* Card Header - Mobile */
        .card-header .d-flex .badge {
            font-size: 0.75rem;
        }

        /* Bottom Section - Mobile */
        .bottom-section {
            margin: 0;
            padding: 0;
        }

        .table-top {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 0 10px;
        }

        .table-top h3 {
            font-size: 1.2rem;
        }

        /* Hide desktop table on mobile */
        #ordersTable {
            display: none !important;
        }

        /* Mobile Table */
        .mobile-table {
            display: block;
            padding: 0 5px;
        }

        .mobile-order-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .order-row:last-child {
            border-bottom: none;
        }

        .order-label {
            font-weight: 600;
            color: #333;
            min-width: 100px;
            font-size: 0.8rem;
        }

        .order-value {
            flex: 1;
            text-align: right;
            font-size: 0.8rem;
            word-break: break-word;
        }

        /* Special handling for multiple food items */
        .food-items {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: flex-end;
        }

        .food-item {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            border: 1px solid #e9ecef;
        }

        /* Status and Payment badges on mobile */
        .mobile-status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            min-width: 80px;
            text-align: center;
        }

        /* Action buttons on mobile */
        .mobile-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-mobile {
            padding: 5px 10px !important;
            font-size: 0.7rem !important;
            border: none !important;
            border-radius: 6px !important;
            text-decoration: none !important;
            display: inline-block !important;
            text-align: center !important;
            min-width: 70px !important;
            color: white !important;
        }

        .btn-mobile-accept {
            background: #4caf50 !important;
        }

        .btn-mobile-unaccept {
            background: #f44336 !important;
        }

        .btn-mobile-history {
            background: #2196f3 !important;
        }

        /* Contact info styling */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: flex-end;
            font-size: 0.75rem;
        }

        /* No Results - Mobile */
        #noResults {
            padding: 30px 15px !important;
        }

        #noResults i {
            font-size: 36px !important;
        }

        #noResults p {
            font-size: 14px !important;
        }

        /* Modals - Mobile */
        .modal-content {
            width: 95% !important;
            margin: 10px !important;
            max-height: 90vh;
        }

        .modal-header {
            padding: 15px !important;
        }

        .modal-header h3 {
            font-size: 1rem !important;
        }

        .modal-body {
            padding: 15px !important;
            max-height: 50vh;
        }

        .status-options, .payment-options {
            grid-template-columns: 1fr !important;
            gap: 8px;
        }

        .status-option, .payment-option {
            padding: 10px !important;
            font-size: 0.85rem;
        }

        .modal-footer {
            padding: 15px !important;
            flex-direction: column;
            gap: 8px;
        }

        .cancel-btn, .confirm-btn, .cancel-payment-btn, .confirm-payment-btn, .close-history-btn {
            width: 100% !important;
            padding: 10px !important;
        }

        /* Success/Error Messages - Mobile */
        .success-message, .error-message {
            margin: 10px 5px;
            padding: 10px;
            flex-direction: row;
            gap: 8px;
        }

        .success-message .text, .error-message .text {
            align-items: flex-start;
        }

        .success-message i, .error-message i {
            font-size: 18px;
        }

        /* Timeline - Mobile */
        .timeline-item {
            padding-left: 25px;
            margin-bottom: 15px;
        }

        .timeline-content {
            padding: 10px;
        }

        .timeline-action {
            font-size: 0.85rem;
        }

        .timeline-staff, .timeline-time, .timeline-status-change, .timeline-notes {
            font-size: 0.75rem;
        }
    }

    /* Small Mobile Devices */
    @media (max-width: 480px) {
        .header h1 {
            font-size: 1.3rem;
        }

        .table-top h3 {
            font-size: 1.1rem;
        }

        .card-title.mb-0.fw-semibold {
            font-size: 0.85rem;
        }

        .btn.btn-sm.btn-outline-secondary {
            padding: 3px 6px;
            font-size: 0.7rem;
        }

        .mobile-order-card {
            padding: 12px;
        }

        .order-label {
            min-width: 80px;
            font-size: 0.75rem;
        }

        .order-value {
            font-size: 0.75rem;
        }

        .btn-mobile {
            min-width: 65px !important;
            padding: 4px 8px !important;
            font-size: 0.65rem !important;
        }

        .food-item {
            font-size: 0.65rem;
        }

        .filter-badge {
            font-size: 0.7rem;
            padding: 2px 6px;
        }
    }

    /* Hide mobile table on desktop */
    @media (min-width: 769px) {
        .mobile-table {
            display: none !important;
        }
    }
</style>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const paymentFilter = document.getElementById('paymentFilter');
    const assignmentFilter = document.getElementById('assignmentFilter');
    const dateFilter = document.getElementById('dateFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const tableRows = document.querySelectorAll('#ordersTable tbody tr');
    const resultCount = document.getElementById('resultCount');
    const noResults = document.getElementById('noResults');
    const activeFiltersDiv = document.getElementById('activeFilters');
    const filterBadgesDiv = document.getElementById('filterBadges');
    const ordersTable = document.getElementById('ordersTable');

    // Apply filters
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;
        const selectedPayment = paymentFilter.value;
        const selectedAssignment = assignmentFilter.value;
        const selectedDate = dateFilter.value;

        let visibleCount = 0;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        tableRows.forEach(row => {
            let visible = true;

            // Search filter
            if (searchTerm) {
                const searchText = row.getAttribute('data-search-text');
                const foodItems = row.querySelector('[data-food-items]')?.getAttribute('data-food-items') || '';
                if (!searchText.includes(searchTerm) && !foodItems.includes(searchTerm)) {
                    visible = false;
                }
            }

            // Status filter
            if (selectedStatus && visible) {
                const rowStatus = row.getAttribute('data-status');
                if (rowStatus !== selectedStatus) {
                    visible = false;
                }
            }

            // Payment filter
            if (selectedPayment && visible) {
                const rowPayment = row.getAttribute('data-payment');
                if (rowPayment !== selectedPayment) {
                    visible = false;
                }
            }

            // Assignment filter
            if (selectedAssignment && visible) {
                const rowAssignment = row.getAttribute('data-assignment');
                if (rowAssignment !== selectedAssignment) {
                    visible = false;
                }
            }

            // Date filter
            if (selectedDate && visible) {
                const rowDate = new Date(row.getAttribute('data-date'));
                rowDate.setHours(0, 0, 0, 0);
                
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                
                const weekStart = new Date(today);
                weekStart.setDate(weekStart.getDate() - weekStart.getDay());
                
                const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

                switch(selectedDate) {
                    case 'today':
                        if (rowDate.getTime() !== today.getTime()) visible = false;
                        break;
                    case 'yesterday':
                        if (rowDate.getTime() !== yesterday.getTime()) visible = false;
                        break;
                    case 'week':
                        if (rowDate < weekStart) visible = false;
                        break;
                    case 'month':
                        if (rowDate < monthStart) visible = false;
                        break;
                }
            }

            // Show/hide row
            if (visible) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update result count
        resultCount.textContent = visibleCount;

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.style.display = 'block';
            ordersTable.style.display = 'none';
        } else {
            noResults.style.display = 'none';
            ordersTable.style.display = '';
        }

        // Update active filters display
        updateActiveFilters();
    }

    // Update active filters badges
    function updateActiveFilters() {
        const filters = [];

        if (searchInput.value.trim()) {
            filters.push({ type: 'search', label: `Search: ${searchInput.value}`, clear: () => searchInput.value = '' });
        }
        if (statusFilter.value) {
            const text = statusFilter.options[statusFilter.selectedIndex].text;
            filters.push({ type: 'status', label: `Status: ${text}`, clear: () => statusFilter.value = '' });
        }
        if (paymentFilter.value) {
            const text = paymentFilter.options[paymentFilter.selectedIndex].text;
            filters.push({ type: 'payment', label: `Payment: ${text}`, clear: () => paymentFilter.value = '' });
        }
        if (assignmentFilter.value) {
            const text = assignmentFilter.options[assignmentFilter.selectedIndex].text;
            filters.push({ type: 'assignment', label: `Assignment: ${text}`, clear: () => assignmentFilter.value = '' });
        }
        if (dateFilter.value) {
            const text = dateFilter.options[dateFilter.selectedIndex].text;
            filters.push({ type: 'date', label: `Date: ${text}`, clear: () => dateFilter.value = '' });
        }

        if (filters.length > 0) {
            activeFiltersDiv.style.display = 'block';
            filterBadgesDiv.innerHTML = filters.map((filter, index) => `
                <span class="filter-badge">
                    ${filter.label}
                    <i class="bi bi-x-circle" data-filter-index="${index}"></i>
                </span>
            `).join('');

            // Add event listeners to remove individual filters
            filterBadgesDiv.querySelectorAll('[data-filter-index]').forEach((badge, index) => {
                badge.addEventListener('click', function() {
                    filters[index].clear();
                    applyFilters();
                });
            });
        } else {
            activeFiltersDiv.style.display = 'none';
            filterBadgesDiv.innerHTML = '';
        }
    }

    // Clear all filters
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        paymentFilter.value = '';
        assignmentFilter.value = '';
        dateFilter.value = '';
        applyFilters();
    });

    // Add event listeners
    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    paymentFilter.addEventListener('change', applyFilters);
    assignmentFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);

    // Mobile table creation (keep your existing function)
    function createMobileTable() {
        if (window.innerWidth <= 768) {
            const table = document.querySelector('.customer-order-index table');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            
            let mobileTable = document.querySelector('.mobile-table');
            if (!mobileTable) {
                mobileTable = document.createElement('div');
                mobileTable.className = 'mobile-table';
                table.parentNode.insertBefore(mobileTable, table);
            }
            
            mobileTable.innerHTML = '';
            
            // Only show visible rows
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    const orderCard = document.createElement('div');
                    orderCard.className = 'mobile-order-card';
                    
                    const orderIdRow = createOrderRow('Order ID', cells[0].innerHTML);
                    const foodItems = Array.from(cells[1].querySelectorAll('span, a')).map(item => item.textContent).filter(Boolean);
                    const foodItemsHTML = foodItems.length > 0 ? 
                        `<div class="food-items">${foodItems.map(item => `<span class="food-item">${item.trim()}</span>`).join('')}</div>` : 
                        'No items';
                    const foodOrderRow = createOrderRow('Food Order', foodItemsHTML);
                    const assignedRow = createOrderRow('Assigned To', cells[2].innerHTML);
                    const statusBadge = cells[3].querySelector('.status-badge');
                    const statusRow = createOrderRow('Order Status', statusBadge ? statusBadge.outerHTML : cells[3].innerHTML);
                    const paymentBadge = cells[4].querySelector('.payment-badge');
                    const paymentRow = createOrderRow('Payment', paymentBadge ? paymentBadge.outerHTML : cells[4].innerHTML);
                    const priceRow = createOrderRow('Total Price', cells[5].innerHTML);
                    const contactRow = createOrderRow('Contact', `<div class="contact-info">${cells[6].innerHTML}</div>`);
                    
                    const actionsDiv = document.createElement('div');
                    actionsDiv.className = 'mobile-actions';
                    
                    const actionButtons = cells[7].querySelectorAll('form, .taken');
                    actionButtons.forEach(button => {
                        if (button.tagName === 'FORM') {
                            const submitBtn = button.querySelector('button');
                            if (submitBtn) {
                                const mobileBtn = document.createElement('button');
                                mobileBtn.className = `btn-mobile ${submitBtn.className.includes('btn-accept') ? 'btn-mobile-accept' : 'btn-mobile-unaccept'}`;
                                mobileBtn.textContent = submitBtn.textContent;
                                mobileBtn.type = 'button';
                                mobileBtn.onclick = () => button.submit();
                                actionsDiv.appendChild(mobileBtn);
                            }
                        } else if (button.classList.contains('taken')) {
                            const span = document.createElement('span');
                            span.className = 'taken';
                            span.textContent = button.textContent;
                            actionsDiv.appendChild(span);
                        }
                    });
                    
                    const historyBtn = cells[8].querySelector('.btn-history');
                    if (historyBtn) {
                        const mobileBtn = document.createElement('button');
                        mobileBtn.className = 'btn-mobile btn-mobile-history';
                        mobileBtn.innerHTML = '<i class="bi bi-clock"></i> History';
                        mobileBtn.onclick = historyBtn.onclick;
                        actionsDiv.appendChild(mobileBtn);
                    }
                    
                    const actionsRow = createOrderRow('Actions', actionsDiv.outerHTML);
                    const dateRow = createOrderRow('Ordered At', cells[9].innerHTML);
                    
                    orderCard.appendChild(orderIdRow);
                    orderCard.appendChild(foodOrderRow);
                    orderCard.appendChild(assignedRow);
                    orderCard.appendChild(statusRow);
                    orderCard.appendChild(paymentRow);
                    orderCard.appendChild(priceRow);
                    orderCard.appendChild(contactRow);
                    orderCard.appendChild(actionsRow);
                    orderCard.appendChild(dateRow);
                    
                    mobileTable.appendChild(orderCard);
                }
            });
            
            table.style.display = 'none';
        } else {
            const table = document.querySelector('.customer-order-index table');
            if (table) table.style.display = '';
            const mobileTable = document.querySelector('.mobile-table');
            if (mobileTable) mobileTable.style.display = 'none';
        }
    }
    
    function createOrderRow(label, value) {
        const row = document.createElement('div');
        row.className = 'order-row';
        row.innerHTML = `
            <div class="order-label">${label}</div>
            <div class="order-value">${value}</div>
        `;
        return row;
    }
    
    createMobileTable();
    window.addEventListener('resize', createMobileTable);
    
    // Recreate mobile table after filtering
    const originalApplyFilters = applyFilters;
    applyFilters = function() {
        originalApplyFilters();
        createMobileTable();
    };
});

// Keep all your existing modal scripts
document.addEventListener('DOMContentLoaded', function() {
    const statusModal = document.getElementById('statusModal');
    const statusBadges = document.querySelectorAll('.status-badge');
    const closeModalBtn = document.querySelector('.close-modal');
    const cancelBtn = document.querySelector('.cancel-btn');
    const confirmBtn = document.querySelector('.confirm-btn');
    const statusOptions = document.querySelectorAll('.status-option');
    const currentStatusSpan = document.getElementById('currentStatus');
    const statusForm = document.getElementById('statusForm');
    
    const paymentModal = document.getElementById('paymentModal');
    const paymentBadges = document.querySelectorAll('.payment-badge');
    const closePaymentModalBtn = document.querySelector('.close-payment-modal');
    const cancelPaymentBtn = document.querySelector('.cancel-payment-btn');
    const confirmPaymentBtn = document.querySelector('.confirm-payment-btn');
    const paymentOptions = document.querySelectorAll('.payment-option');
    const currentPaymentStatusSpan = document.getElementById('currentPaymentStatus');
    const paymentForm = document.getElementById('paymentForm');
    
    const historyModal = document.getElementById('historyModal');
    const closeHistoryBtn = document.querySelector('.close-history-modal');
    const closeHistoryBtn2 = document.querySelector('.close-history-btn');
    
    let currentOrderId = null;
    let selectedStatus = null;
    let currentStatus = null;
    let selectedPaymentStatus = null;
    let currentPaymentStatus = null;

    function getStatusClass(status) {
        const statusClasses = {
            'ordered': 'status-ordered',
            'preparing': 'status-preparing',
            'ready_to_deliver': 'status-ready',
            'delivery_on_the_way': 'status-delivery',
            'delivered': 'status-delivered',
            'completed': 'status-completed',
            'cancelled': 'status-cancelled'
        };
        return statusClasses[status] || 'status-ordered';
    }

    function formatStatusDisplay(status) {
        return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function getPaymentClass(isPaid) {
        return isPaid === '1' ? 'payment-paid' : 'payment-not-paid';
    }

    function getPaymentDisplay(isPaid) {
        return isPaid === '1' ? 'Paid' : 'Not Paid';
    }

    if (statusBadges.length > 0 && statusModal) {
        statusBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                const orderId = this.closest('.order-status-cell').getAttribute('data-order-id');
                currentStatus = this.getAttribute('data-status');
                currentOrderId = orderId;
                currentStatusSpan.textContent = formatStatusDisplay(currentStatus);
                currentStatusSpan.className = 'current-status-badge ' + getStatusClass(currentStatus);
                statusOptions.forEach(option => {
                    option.classList.remove('selected');
                    if (option.getAttribute('data-status') === currentStatus) {
                        option.classList.add('selected');
                    }
                });
                confirmBtn.disabled = true;
                selectedStatus = null;
                statusForm.action = `/staff/customer-order/update-order/${orderId}`;
                statusModal.classList.add('active');
            });
        });

        statusOptions.forEach(option => {
            option.addEventListener('click', function() {
                const newStatus = this.getAttribute('data-status');
                if (newStatus === currentStatus) {
                    confirmBtn.disabled = true;
                    selectedStatus = null;
                    return;
                }
                statusOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedStatus = newStatus;
                confirmBtn.disabled = false;
            });
        });

        function closeStatusModal() {
            statusModal.classList.remove('active');
            currentOrderId = null;
            selectedStatus = null;
            currentStatus = null;
        }

        if (closeModalBtn) closeModalBtn.addEventListener('click', closeStatusModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeStatusModal);

        statusForm.addEventListener('submit', function(e) {
            if (!selectedStatus) {
                e.preventDefault();
                return;
            }
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'order_status';
            statusInput.value = selectedStatus;
            this.appendChild(statusInput);
        });

        statusModal.addEventListener('click', function(e) {
            if (e.target === statusModal) closeStatusModal();
        });
    }

    if (paymentBadges.length > 0 && paymentModal) {
        paymentBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                const orderId = this.closest('.payment-status-cell').getAttribute('data-order-id');
                currentPaymentStatus = this.getAttribute('data-is-paid');
                currentOrderId = orderId;
                currentPaymentStatusSpan.textContent = getPaymentDisplay(currentPaymentStatus);
                currentPaymentStatusSpan.className = 'current-payment-badge ' + getPaymentClass(currentPaymentStatus);
                paymentOptions.forEach(option => {
                    option.classList.remove('selected');
                    if (option.getAttribute('data-is-paid') === currentPaymentStatus) {
                        option.classList.add('selected');
                    }
                });
                confirmPaymentBtn.disabled = true;
                selectedPaymentStatus = null;
                paymentForm.action = `/staff/customer-order/update-order-payment/${orderId}`;
                paymentModal.classList.add('active');
            });
        });

        paymentOptions.forEach(option => {
            option.addEventListener('click', function() {
                const newPaymentStatus = this.getAttribute('data-is-paid');
                if (newPaymentStatus === currentPaymentStatus) {
                    confirmPaymentBtn.disabled = true;
                    selectedPaymentStatus = null;
                    return;
                }
                paymentOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedPaymentStatus = newPaymentStatus;
                confirmPaymentBtn.disabled = false;
            });
        });

        function closePaymentModal() {
            paymentModal.classList.remove('active');
            currentOrderId = null;
            selectedPaymentStatus = null;
            currentPaymentStatus = null;
        }

        if (closePaymentModalBtn) closePaymentModalBtn.addEventListener('click', closePaymentModal);
        if (cancelPaymentBtn) cancelPaymentBtn.addEventListener('click', closePaymentModal);

        paymentForm.addEventListener('submit', function(e) {
            if (!selectedPaymentStatus) {
                e.preventDefault();
                return;
            }
            const paymentInput = document.createElement('input');
            paymentInput.type = 'hidden';
            paymentInput.name = 'is_paid';
            paymentInput.value = selectedPaymentStatus;
            this.appendChild(paymentInput);
        });

        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) closePaymentModal();
        });
    }

    if (historyModal) {
        function closeHistoryModal() {
            historyModal.classList.remove('active');
        }
        if (closeHistoryBtn) closeHistoryBtn.addEventListener('click', closeHistoryModal);
        if (closeHistoryBtn2) closeHistoryBtn2.addEventListener('click', closeHistoryModal);
        historyModal.addEventListener('click', function(e) {
            if (e.target === historyModal) closeHistoryModal();
        });
    }
});

function showOrderHistory(orderId) {
    fetch(`/staff/customer-order/${orderId}/history/json`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            document.getElementById('historyOrderId').textContent = orderId.substring(0, 8);
            const timeline = document.getElementById('historyTimeline');
            if (data.histories.length === 0) {
                timeline.innerHTML = '<p>No history available for this order.</p>';
            } else {
                timeline.innerHTML = data.histories.map(history => `
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-action">${history.action.replace('_', ' ')}</div>
                            ${history.old_status && history.new_status ? 
                                `<div class="timeline-status-change">
                                    ${formatStatusDisplay(history.old_status)} → ${formatStatusDisplay(history.new_status)}
                                </div>` : ''
                            }
                            <div class="timeline-staff">By: ${history.staff_name}</div>
                            <div class="timeline-time">${history.created_at}</div>
                            ${history.notes ? `<div class="timeline-notes">${history.notes}</div>` : ''}
                        </div>
                    </div>
                `).join('');
            }
            document.getElementById('historyModal').classList.add('active');
        })
        .catch(error => {
            console.error('Error fetching order history:', error);
            alert('Error loading order history. Please try again.');
        });
}

function formatStatusDisplay(status) {
    return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}
</script>
@endsection