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
                                            <h3 class="fw-bold text-info mb-0">{{  @$totalCount }}</h3>
                                        </div>
                                        <div class="bg-info bg-opacity-10 p-3 rounded">
                                            <i class="bi bi-list-ul text-info fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

        <!-- Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">All Orders</h5>
                    <div class="d-flex">
                        <input type="text" class="form-control form-control-sm me-2" placeholder="Search orders...">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

                    <div class="bottom-section">
                        <div class="table-top">
                            <h3>Manage Orders</h3>
                            <div class="button">
                                <a href="{{ route('customer-order-create') }}" class="add">
                                    <i class='bx bxs-plus-circle'></i><span>Check Table</span>
                                </a>
                            </div>
                        </div>

                        <table>
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
                                    <tr>
                                        <td>
                                            <span title="{{ $order->id }}">#{{ Str::limit($order->id, 8) }}</span>
                                        </td>
                                        <td>
                                            @foreach ($order->customerOrderDetail as $orderDetail)
                                                {{ Str::limit($orderDetail->foodMenu->name, 10) }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
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
                                        <td class="order-status-cell" data-order-id="{{ $order->id }}">
                                            <div class="status-badge {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusClass($order->order_status) }}" 
                                                 data-status="{{ $order->order_status }}"
                                                 data-current-status="{{ $order->order_status }}">
                                                {{ App\Http\Controllers\Staff\Order\OrderControler::getStatusDisplay($order->order_status) }}
                                            </div>
                                        </td>
                                        <td class="payment-status-cell" data-order-id="{{ $order->id }}">
                                            <div class="payment-badge {{ $order->isPaid ? 'payment-paid' : 'payment-not-paid' }}" 
                                                 data-is-paid="{{ $order->isPaid }}"
                                                 data-current-paid="{{ $order->isPaid }}">
                                                {{ $order->isPaid ? 'Paid' : 'Not Paid' }}
                                            </div>
                                        </td>
                                        <td>${{ number_format($order->order_total_price, 2) }}</td>
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
                    </div>
                </div>
            </main>
        </div>

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

    /* Status Colors */
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

    /* Payment Badge Styles */
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

    /* Assignment Badges */
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

    /* Action Buttons - FIXED TEXT VISIBILITY */
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

    /* Modal Styles */
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

    /* Statistic Cards */
    .statistic {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .statistic .item1, .statistic .item2, .statistic .item3 {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .statistic i {
        font-size: 32px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .statistic .item1 i {
        background: #e8f5e8;
        color: #4caf50;
    }

    .statistic .item2 i {
        background: #e3f2fd;
        color: #2196f3;
    }

    .statistic .item3 i {
        background: #fff3e0;
        color: #ff9800;
    }

    .statistic .data {
        display: flex;
        flex-direction: column;
    }

    .statistic .title {
        font-size: 14px;
        color: #666;
        margin-bottom: 4px;
    }

    .statistic .data span:last-child {
        font-size: 18px;
        font-weight: 600;
        color: #333;
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

    .button .add {
        background: #4caf50;
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .button .add:hover {
        background: #45a049;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
        .statistic {
            grid-template-columns: 1fr;
        }
        
        .table-top {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
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
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Order Status Modal - Check if elements exist
        const statusModal = document.getElementById('statusModal');
        const statusBadges = document.querySelectorAll('.status-badge');
        const closeModalBtn = document.querySelector('.close-modal');
        const cancelBtn = document.querySelector('.cancel-btn');
        const confirmBtn = document.querySelector('.confirm-btn');
        const statusOptions = document.querySelectorAll('.status-option');
        const currentStatusSpan = document.getElementById('currentStatus');
        const statusForm = document.getElementById('statusForm');
        
        // Payment Status Modal - Check if elements exist
        const paymentModal = document.getElementById('paymentModal');
        const paymentBadges = document.querySelectorAll('.payment-badge');
        const closePaymentModalBtn = document.querySelector('.close-payment-modal');
        const cancelPaymentBtn = document.querySelector('.cancel-payment-btn');
        const confirmPaymentBtn = document.querySelector('.confirm-payment-btn');
        const paymentOptions = document.querySelectorAll('.payment-option');
        const currentPaymentStatusSpan = document.getElementById('currentPaymentStatus');
        const paymentForm = document.getElementById('paymentForm');
        
        // History Modal - Check if elements exist
        const historyModal = document.getElementById('historyModal');
        const closeHistoryBtn = document.querySelector('.close-history-modal');
        const closeHistoryBtn2 = document.querySelector('.close-history-btn');
        
        let currentOrderId = null;
        let selectedStatus = null;
        let currentStatus = null;
        let selectedPaymentStatus = null;
        let currentPaymentStatus = null;

        // Helper functions...
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

        // Only add event listeners if elements exist
        if (statusBadges.length > 0 && statusModal && closeModalBtn && cancelBtn && confirmBtn && statusOptions.length > 0 && currentStatusSpan && statusForm) {
            // Open order status modal when clicking on status badge
            statusBadges.forEach(badge => {
                badge.addEventListener('click', function() {
                    const orderId = this.closest('.order-status-cell').getAttribute('data-order-id');
                    currentStatus = this.getAttribute('data-status');
                    
                    currentOrderId = orderId;
                    
                    // Update current status display with badge styling
                    currentStatusSpan.textContent = formatStatusDisplay(currentStatus);
                    currentStatusSpan.className = 'current-status-badge ' + getStatusClass(currentStatus);
                    
                    // Reset selection
                    statusOptions.forEach(option => {
                        option.classList.remove('selected');
                        if (option.getAttribute('data-status') === currentStatus) {
                            option.classList.add('selected');
                        }
                    });
                    
                    confirmBtn.disabled = true;
                    selectedStatus = null;
                    
                    // Update form action
                    statusForm.action = 'http://hoteldash.test/staff/customer-order/update-order/:id'.replace(':id', orderId);
                    
                    // Show modal
                    statusModal.classList.add('active');
                });
            });

            // Select order status option
            statusOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const newStatus = this.getAttribute('data-status');
                    
                    // Don't allow selecting current status
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

            // Close order status modal functions
            function closeStatusModal() {
                statusModal.classList.remove('active');
                currentOrderId = null;
                selectedStatus = null;
                currentStatus = null;
            }

            closeModalBtn.addEventListener('click', closeStatusModal);
            cancelBtn.addEventListener('click', closeStatusModal);

            // Order status form submission
            statusForm.addEventListener('submit', function(e) {
                if (!selectedStatus) {
                    e.preventDefault();
                    return;
                }
                
                // Add the selected status to the form data
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'order_status';
                statusInput.value = selectedStatus;
                this.appendChild(statusInput);
            });

            // Close modal when clicking outside
            statusModal.addEventListener('click', function(e) {
                if (e.target === statusModal) {
                    closeStatusModal();
                }
            });
        }

        // Payment modal event listeners - only if elements exist
        if (paymentBadges.length > 0 && paymentModal && closePaymentModalBtn && cancelPaymentBtn && confirmPaymentBtn && paymentOptions.length > 0 && currentPaymentStatusSpan && paymentForm) {
            // Open payment status modal when clicking on payment badge
            paymentBadges.forEach(badge => {
                badge.addEventListener('click', function() {
                    const orderId = this.closest('.payment-status-cell').getAttribute('data-order-id');
                    currentPaymentStatus = this.getAttribute('data-is-paid');
                    
                    currentOrderId = orderId;
                    
                    // Update current payment status display with badge styling
                    currentPaymentStatusSpan.textContent = getPaymentDisplay(currentPaymentStatus);
                    currentPaymentStatusSpan.className = 'current-payment-badge ' + getPaymentClass(currentPaymentStatus);
                    
                    // Reset selection
                    paymentOptions.forEach(option => {
                        option.classList.remove('selected');
                        if (option.getAttribute('data-is-paid') === currentPaymentStatus) {
                            option.classList.add('selected');
                        }
                    });
                    
                    confirmPaymentBtn.disabled = true;
                    selectedPaymentStatus = null;
                    
                    // Update form action for payment
                    paymentForm.action = 'http://hoteldash.test/staff/customer-order/update-order-payment/:id'.replace(':id', orderId);
                    
                    // Show modal
                    paymentModal.classList.add('active');
                });
            });

            // Select payment status option
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const newPaymentStatus = this.getAttribute('data-is-paid');
                    
                    // Don't allow selecting current payment status
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

            // Close payment status modal functions
            function closePaymentModal() {
                paymentModal.classList.remove('active');
                currentOrderId = null;
                selectedPaymentStatus = null;
                currentPaymentStatus = null;
            }

            closePaymentModalBtn.addEventListener('click', closePaymentModal);
            cancelPaymentBtn.addEventListener('click', closePaymentModal);

            // Payment status form submission
            paymentForm.addEventListener('submit', function(e) {
                if (!selectedPaymentStatus) {
                    e.preventDefault();
                    return;
                }
                
                // Add the selected payment status to the form data
                const paymentInput = document.createElement('input');
                paymentInput.type = 'hidden';
                paymentInput.name = 'is_paid';
                paymentInput.value = selectedPaymentStatus;
                this.appendChild(paymentInput);
            });

            // Close modal when clicking outside
            paymentModal.addEventListener('click', function(e) {
                if (e.target === paymentModal) {
                    closePaymentModal();
                }
            });
        }

        // History modal event listeners - only if elements exist
        if (historyModal && closeHistoryBtn && closeHistoryBtn2) {
            // Close history modal function
            function closeHistoryModal() {
                historyModal.classList.remove('active');
            }

            closeHistoryBtn.addEventListener('click', closeHistoryModal);
            closeHistoryBtn2.addEventListener('click', closeHistoryModal);

            // Close modal when clicking outside
            historyModal.addEventListener('click', function(e) {
                if (e.target === historyModal) {
                    closeHistoryModal();
                }
            });
        }
    });

    // Global function to show order history - FIXED URL
    function showOrderHistory(orderId) {
        // Use the correct URL - fixed the route
        fetch(`/staff/customer-order/${orderId}/history/json`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
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

    // Helper function to format status display
    function formatStatusDisplay(status) {
        return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
</script>
@endsection