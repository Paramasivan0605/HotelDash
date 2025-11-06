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
                                    <th> Address </th>
                                    <th>Ordered At</th>
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
                                        <td>{{ $order->payment_type }}</td>
                                        <td>${{ number_format($order->order_total_price, 2) }}</td>
                                       <td>
                                            {{ $order->customer_contact ?? 'N/A' }}
                                            @if(!empty($order->customer->mobile))
                                                / {{ $order->customer->mobile }}
                                            @endif
                                        </td>
                                        <td>{{ $order->customer->address ?? 'N/A' }}</td>
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

    </section>

@endsection

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
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Status Colors */
    .status-ordered {
        background-color: #e3f2fd;
        color: #1976d2;
        border-color: #bbdefb;
    }

    .status-preparing {
        background-color: #fff3e0;
        color: #f57c00;
        border-color: #ffe0b2;
    }

    .status-ready {
        background-color: #e8f5e8;
        color: #388e3c;
        border-color: #c8e6c9;
    }

    .status-delivery {
        background-color: #e3f2fd;
        color: #0288d1;
        border-color: #b3e5fc;
    }

    .status-delivered {
        background-color: #e8f5e8;
        color: #2e7d32;
        border-color: #a5d6a7;
    }

    .status-completed {
        background-color: #e8f5e8;
        color: #1b5e20;
        border-color: #81c784;
    }

    .status-cancelled {
        background-color: #ffebee;
        color: #c62828;
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
    }

    .payment-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .payment-paid {
        background-color: #e8f5e8;
        color: #2e7d32;
        border-color: #a5d6a7;
    }

    .payment-not-paid {
        background-color: #ffebee;
        color: #c62828;
        border-color: #ffcdd2;
    }

    /* Modal Styles */
    .status-modal, .payment-modal {
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

    .status-modal.active, .payment-modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        overflow: hidden;
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
    }

    .close-modal, .close-payment-modal {
        font-size: 24px;
        cursor: pointer;
        color: #666;
        line-height: 1;
    }

    .close-modal:hover, .close-payment-modal:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-body p {
        margin-bottom: 15px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .current-status-badge, .current-payment-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
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
    }

    .status-option:hover, .payment-option:hover {
        border-color: #2196f3;
        background-color: #f5f9ff;
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
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background-color: #f8f9fa;
    }

    .cancel-btn, .confirm-btn, .cancel-payment-btn, .confirm-payment-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .cancel-btn, .cancel-payment-btn {
        background-color: #f5f5f5;
        color: #666;
    }

    .cancel-btn:hover, .cancel-payment-btn:hover {
        background-color: #e0e0e0;
    }

    .confirm-btn, .confirm-payment-btn {
        background-color: #4caf50;
        color: white;
    }

    .confirm-btn:disabled, .confirm-payment-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .confirm-btn:not(:disabled):hover, .confirm-payment-btn:not(:disabled):hover {
        background-color: #45a049;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Order Status Modal
        const statusModal = document.getElementById('statusModal');
        const statusBadges = document.querySelectorAll('.status-badge');
        const closeModalBtn = document.querySelector('.close-modal');
        const cancelBtn = document.querySelector('.cancel-btn');
        const confirmBtn = document.querySelector('.confirm-btn');
        const statusOptions = document.querySelectorAll('.status-option');
        const currentStatusSpan = document.getElementById('currentStatus');
        const statusForm = document.getElementById('statusForm');
        
        // Payment Status Modal
        const paymentModal = document.getElementById('paymentModal');
        const paymentBadges = document.querySelectorAll('.payment-badge');
        const closePaymentModalBtn = document.querySelector('.close-payment-modal');
        const cancelPaymentBtn = document.querySelector('.cancel-payment-btn');
        const confirmPaymentBtn = document.querySelector('.confirm-payment-btn');
        const paymentOptions = document.querySelectorAll('.payment-option');
        const currentPaymentStatusSpan = document.getElementById('currentPaymentStatus');
        const paymentForm = document.getElementById('paymentForm');
        
        let currentOrderId = null;
        let selectedStatus = null;
        let currentStatus = null;
        let selectedPaymentStatus = null;
        let currentPaymentStatus = null;

        // Helper function to get status class
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

        // Helper function to format status display
        function formatStatusDisplay(status) {
            return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        // Helper function to get payment class
        function getPaymentClass(isPaid) {
            return isPaid === '1' ? 'payment-paid' : 'payment-not-paid';
        }

        // Helper function to get payment display text
        function getPaymentDisplay(isPaid) {
            return isPaid === '1' ? 'Paid' : 'Not Paid';
        }

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
                statusForm.action = '{{ route("update-order", ":id") }}'.replace(':id', orderId);
                
                // Show modal
                statusModal.classList.add('active');
            });
        });

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
                paymentForm.action = '{{ route("update-order-payment", ":id") }}'.replace(':id', orderId);
                
                // Show modal
                paymentModal.classList.add('active');
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

        // Close order status modal functions
        function closeStatusModal() {
            statusModal.classList.remove('active');
            currentOrderId = null;
            selectedStatus = null;
            currentStatus = null;
        }

        // Close payment status modal functions
        function closePaymentModal() {
            paymentModal.classList.remove('active');
            currentOrderId = null;
            selectedPaymentStatus = null;
            currentPaymentStatus = null;
        }

        closeModalBtn.addEventListener('click', closeStatusModal);
        cancelBtn.addEventListener('click', closeStatusModal);

        closePaymentModalBtn.addEventListener('click', closePaymentModal);
        cancelPaymentBtn.addEventListener('click', closePaymentModal);

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

        // Close modals when clicking outside
        statusModal.addEventListener('click', function(e) {
            if (e.target === statusModal) {
                closeStatusModal();
            }
        });

        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                closePaymentModal();
            }
        });
    });
</script>