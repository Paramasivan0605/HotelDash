/*
*  ------------------- Scrolling Top Bar ----------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const topbar = document.querySelector('.topbar');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 0) {
            topbar.classList.add('scrolled');
        } else {
            topbar.classList.remove('scrolled');
        }
    });
});

/*
*  ---------------------------- Choose Delivery Option ------------------------------
*/
document.addEventListener('DOMContentLoaded', function() {
    const deliveryModal = document.getElementById('deliveryOptionModal');
    const menuSection = document.getElementById('menuSection');
    let selectedOption = null;
    let selectedLocationId = null;
    
    if (deliveryModal) {
        const bsDeliveryModal = new bootstrap.Modal(deliveryModal);
        const navEntries = performance.getEntriesByType("navigation");
        const isReload = navEntries.length > 0 && navEntries[0].type === "reload";

        if (!isReload) {
            bsDeliveryModal.show();
        } else {
            if (menuSection) {
                menuSection.style.display = 'block';
            }

            selectedOption = sessionStorage.getItem('delivery_type');
            selectedLocationId = sessionStorage.getItem('location_id');
            
            if (selectedOption) {
                document.querySelectorAll('.add-to-cart').forEach(btn => {
                    btn.dataset.deliveryType = selectedOption;
                    btn.dataset.locationId = selectedLocationId;
                });
            }
        }

        document.querySelectorAll('.delivery-option-btn').forEach(button => {
            button.addEventListener('click', function() {
                selectedOption = this.dataset.option;
                selectedLocationId = this.dataset.locationId;

                sessionStorage.setItem('delivery_type', selectedOption);
                sessionStorage.setItem('location_id', selectedLocationId);
                
                bsDeliveryModal.hide();
                if (menuSection) {
                    menuSection.style.display = 'block';
                }

                document.querySelectorAll('.add-to-cart').forEach(btn => {
                    btn.dataset.deliveryType = selectedOption;
                    btn.dataset.locationId = selectedLocationId;
                });
            });
        });
    }
});

/*
*  ---------------------------- Add to Cart - FIXED & COMPLETE ------------------------------
*/
document.addEventListener('DOMContentLoaded', (event) => {
    const cartList = document.querySelector('.cart-list');
    const addProduct = document.querySelectorAll('.add-to-cart');
    let cart = JSON.parse(localStorage.getItem('cart')) || {};

    const tableNumberInput = document.querySelector('input[name="table_number"]');
    const customerContactInput = document.querySelector('input[name="customer_contact"]');
    const customerAddressInput = document.querySelector('textarea[name="customer_address"]');
    const confirmOrderBtn = document.querySelector('.confirm-order');
    const cartBadge = document.getElementById('cart-quantity');

    // Initialize cart display
    loadCustomerAddress();
    updateCart();
    updateConfirmButtonState();

    // Contact number input listener
    if (customerContactInput) {
        customerContactInput.addEventListener('input', updateConfirmButtonState);
    }

    // Address input listener
    if (customerAddressInput) {
        customerAddressInput.addEventListener('input', () => {
            updateConfirmButtonState();
            sessionStorage.setItem('temp_address', customerAddressInput.value);
        });
    }

    // Table number input listener
    if (tableNumberInput) {
        tableNumberInput.addEventListener('input', updateConfirmButtonState);
    }

    // Add product to cart
    addProduct.forEach(button => {
        button.addEventListener('click', () => {
            const foodId = button.getAttribute('data-food-id');
            const foodImage = button.getAttribute('data-food-image');
            const foodName = button.getAttribute('data-food-name');
            const foodPrice = button.getAttribute('data-food-price');
            const deliveryType = button.getAttribute('data-delivery-type') || localStorage.getItem('selectedDeliveryType') || '';
            const locationId = button.getAttribute('data-location-id') || localStorage.getItem('location_id') || '';

            if (!locationId) {
                showToast('⚠️ Location ID is missing. Please select a location first.', 'warning');
                return;
            }
            if (!deliveryType) {
                showToast('⚠️ Please select a delivery type before adding to cart!', 'warning');
                return;
            }

            // Get fresh cart data from localStorage
            cart = JSON.parse(localStorage.getItem('cart')) || {};

            const firstCartItemKey = Object.keys(cart)[0];
            const cartDeliveryType = firstCartItemKey ? cart[firstCartItemKey].deliveryType : null;
            const cartLocationId = firstCartItemKey ? cart[firstCartItemKey].locationId : null;

            if (firstCartItemKey) {
                if (cartDeliveryType && cartDeliveryType !== deliveryType) {
                    showToast(`⚠️ You already have items in your cart with delivery type "${cartDeliveryType}". Please clear your cart before adding items with "${deliveryType}".`, 'warning');
                    return;
                }

                if (String(cartLocationId) !== String(locationId)) {
                    showToast(`⚠️ You already have items in your cart from a different location. Please clear your cart before adding items from this location.`, 'warning');
                    return;
                }
            }

            if (cart[foodId]) {
                cart[foodId].quantity++;
            } else {
                cart[foodId] = {
                    image: foodImage,
                    name: foodName,
                    price: parseFloat(foodPrice),
                    deliveryType: deliveryType,
                    locationId: locationId,
                    quantity: 1
                };
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCart();
            showToast(`✓ ${foodName} added to cart!`, 'success');
        });
    });

    // Minus button
    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('minus') || event.target.closest('.minus')) {
            const button = event.target.classList.contains('minus') ? event.target : event.target.closest('.minus');
            const foodId = button.getAttribute('data-food-id');
            cart = JSON.parse(localStorage.getItem('cart')) || {};
            
            if (cart[foodId] && cart[foodId].quantity > 1) {
                cart[foodId].quantity--;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }
    });

    // Plus button
    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('plus') || event.target.closest('.plus')) {
            const button = event.target.classList.contains('plus') ? event.target : event.target.closest('.plus');
            const foodId = button.getAttribute('data-food-id');
            cart = JSON.parse(localStorage.getItem('cart')) || {};
            
            if (cart[foodId]) {
                cart[foodId].quantity++;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }
    });

    // Delete button
    document.addEventListener('click', (event) => {
        if (event.target.closest('.delete-cart-item')) {
            const foodId = event.target.closest('.delete-cart-item').getAttribute('data-food-id');
            cart = JSON.parse(localStorage.getItem('cart')) || {};
            
            if (cart[foodId]) {
                const foodName = cart[foodId].name;
                delete cart[foodId];
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
                updateConfirmButtonState();
                showToast(`${foodName} removed from cart`, 'warning');
            }
        }
    });

    // Change delivery type
    const changeDeliveryBtn = document.getElementById('changeDeliveryType');
    if (changeDeliveryBtn) {
        changeDeliveryBtn.addEventListener('click', () => {
            const changeDeliveryModal = new bootstrap.Modal(document.getElementById('changeDeliveryModal'));
            changeDeliveryModal.show();
        });
    }

    document.querySelectorAll('.btn-delivery-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const newDeliveryType = this.getAttribute('data-option');
            cart = JSON.parse(localStorage.getItem('cart')) || {};
            
            for (const foodId in cart) {
                cart[foodId].deliveryType = newDeliveryType;
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            localStorage.setItem('selectedDeliveryType', newDeliveryType);
            
            document.querySelectorAll('.add-to-cart').forEach(addBtn => {
                addBtn.setAttribute('data-delivery-type', newDeliveryType);
            });
            
            updateCart();
            
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('changeDeliveryModal'));
            if (modalInstance) {
                modalInstance.hide();
            }
            
            showToast(`Order type changed to ${newDeliveryType}`, 'success');
        });
    });

    // ================== UPDATE CART DISPLAY - FIXED VERSION ==================
    function updateCart() {
        cartList.innerHTML = '';

        // Get fresh cart data
        cart = JSON.parse(localStorage.getItem('cart')) || {};
        
        let totalAmount = 0;
        let totalItemCount = 0;

        if (Object.keys(cart).length === 0) {
            cartList.innerHTML = `
                <li class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                    <span class="empty">No items in cart</span>
                </li>
            `;
        } else {
            for (const foodId in cart) {
                const product = cart[foodId];
                const productTotalPrice = product.price * product.quantity;

                const listItem = document.createElement('li');
                listItem.className = 'mb-3';
                listItem.innerHTML = `
                    <div class="card cart-item-card border shadow-sm">
                        <div class="card-body p-2 p-sm-3">
                            <div class="row g-2 align-items-center">
                                <!-- Product Image (Hidden on very small screens) -->
                                <div class="col-3 col-sm-2 d-none d-sm-block">
                                    <img src="${product.image}" 
                                         alt="${product.name}" 
                                         class="cart-product-img w-100 rounded">
                                </div>
                                
                                <!-- Product Details -->
                                <div class="col-8 col-sm-6">
                                    <h6 class="mb-1 fw-semibold text-truncate" title="${product.name}">${product.name}</h6>
                                    <small class="text-muted d-block">RM ${product.price.toFixed(2)} each</small>
                                    <div class="fw-bold text-primary mt-1">RM ${productTotalPrice.toFixed(2)}</div>
                                </div>
                                
                                <!-- Quantity Controls & Delete -->
                                <div class="col-4 col-sm-4">
                                    <!-- Quantity Controls -->
                                    <div class="d-flex align-items-center justify-content-end gap-1 mb-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary qty-btn minus" 
                                                data-food-id="${foodId}">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="fw-bold mx-1 small">${product.quantity}</span>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary qty-btn plus" 
                                                data-food-id="${foodId}">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-danger w-100 delete-cart-item" 
                                            data-food-id="${foodId}">
                                        <i class="bi bi-trash"></i>
                                        <span class="d-none d-sm-inline ms-1">Remove</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                cartList.appendChild(listItem);
                totalAmount += productTotalPrice;
                totalItemCount += product.quantity;
            }
        }

        // Update cart summary
        document.getElementById('cart-total-amount').textContent = `RM ${totalAmount.toFixed(2)}`;
        document.getElementById('cart-item-count').textContent = `${totalItemCount} item${totalItemCount !== 1 ? 's' : ''}`;
        
        // Update cart badge
        if (cartBadge) {
            if (totalItemCount > 0) {
                cartBadge.textContent = totalItemCount;
                cartBadge.style.display = 'inline-block';
            } else {
                cartBadge.textContent = '0';
                cartBadge.style.display = 'none';
            }
        }

        // Update delivery type display
        const deliveryInfoDiv = document.querySelector('.delivery-type-info');
        const deliveryTypeSpan = document.getElementById('selected-delivery-type');
        const tableNumberSection = document.querySelector('.table-number');
        const addressSection = document.querySelector('.customer-address');
        const cashNote = document.querySelector('.cash-note');

        if (Object.keys(cart).length > 0) {
            const firstItem = cart[Object.keys(cart)[0]];
            const deliveryType = firstItem.deliveryType || '';

            if (deliveryTypeSpan) deliveryTypeSpan.textContent = deliveryType;
            if (deliveryInfoDiv) deliveryInfoDiv.style.display = 'block';

            if (deliveryType === 'Doorstep Delivery') {
                if (tableNumberSection) tableNumberSection.style.display = 'none';
                if (addressSection) addressSection.style.display = 'block';
                if (cashNote) cashNote.style.display = 'block';
                if (tableNumberInput) tableNumberInput.value = '';
            } else if (deliveryType === 'Restaurant Dine-in') {
                if (tableNumberSection) tableNumberSection.style.display = 'block';
                if (addressSection) addressSection.style.display = 'none';
                if (cashNote) cashNote.style.display = 'block';
            } else {
                if (tableNumberSection) tableNumberSection.style.display = 'none';
                if (addressSection) addressSection.style.display = 'none';
                if (cashNote) cashNote.style.display = 'block';
                if (tableNumberInput) tableNumberInput.value = '';
            }
        } else {
            if (deliveryInfoDiv) deliveryInfoDiv.style.display = 'none';
            if (tableNumberSection) tableNumberSection.style.display = 'none';
            if (addressSection) addressSection.style.display = 'none';
            if (cashNote) cashNote.style.display = 'none';
        }

        updateConfirmButtonState();
    }

    // Update confirm button state
    function updateConfirmButtonState() {
        if (!confirmOrderBtn) return;

        const tableNumberValue = tableNumberInput ? tableNumberInput.value.trim() : '';
        const isCartEmpty = Object.keys(cart).length === 0;
        const hasContact = customerContactInput ? customerContactInput.value.trim() !== '' : false;
        const addressValue = customerAddressInput ? customerAddressInput.value.trim() : '';

        let requiresTable = false;
        let requiresAddress = false;
        let hasDeliveryType = false;

        if (Object.keys(cart).length > 0) {
            const firstItem = cart[Object.keys(cart)[0]];
            if (firstItem.deliveryType) {
                hasDeliveryType = true;
                requiresTable = firstItem.deliveryType === 'Restaurant Dine-in';
                requiresAddress = firstItem.deliveryType === 'Doorstep Delivery';
            }
        }

        const isEnabled = !isCartEmpty && 
                          hasContact && 
                          hasDeliveryType && 
                          (!requiresTable || tableNumberValue !== '') && 
                          (!requiresAddress || addressValue !== '');

        confirmOrderBtn.disabled = !isEnabled;
        
        // Update button appearance
        if (isEnabled) {
            confirmOrderBtn.classList.remove('btn-secondary');
            confirmOrderBtn.classList.add('btn-success', 'gradient-button');
        } else {
            confirmOrderBtn.classList.remove('btn-success', 'gradient-button');
            confirmOrderBtn.classList.add('btn-secondary');
        }
    }

    // Load customer address
    function loadCustomerAddress() {
        const customerId = document.body.getAttribute('data-customer-id');
        if (customerId && customerId.trim() !== '' && customerAddressInput) {
            fetch(`/customer/address/${customerId}`)
                .then(response => response.ok ? response.json() : Promise.reject())
                .then(data => {
                    if (data.address && data.address.trim() !== '') {
                        customerAddressInput.value = data.address;
                        updateConfirmButtonState();
                    } else {
                        const tempAddress = sessionStorage.getItem('temp_address');
                        if (tempAddress) {
                            customerAddressInput.value = tempAddress;
                            updateConfirmButtonState();
                        }
                    }
                })
                .catch(() => {
                    const tempAddress = sessionStorage.getItem('temp_address');
                    if (tempAddress && customerAddressInput) {
                        customerAddressInput.value = tempAddress;
                        updateConfirmButtonState();
                    }
                });
        } else if (customerAddressInput) {
            const tempAddress = sessionStorage.getItem('temp_address');
            if (tempAddress) {
                customerAddressInput.value = tempAddress;
                updateConfirmButtonState();
            }
        }
    }

    // Toast notification helper
    function showToast(message, type) {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }

    // ================== PAYMENT AND CONFIRMATION FLOW ==================
    let paymentModal, finalConfirmationModal;

    function initializeModals() {
        if (!paymentModal) {
            const paymentModalEl = document.getElementById('paymentConfirmationModal');
            if (paymentModalEl) {
                paymentModal = new bootstrap.Modal(paymentModalEl);
            }
        }
        if (!finalConfirmationModal) {
            const finalModalEl = document.getElementById('finalConfirmationModal');
            if (finalModalEl) {
                finalConfirmationModal = new bootstrap.Modal(finalModalEl);
            }
        }
    }

    if (confirmOrderBtn) {
        confirmOrderBtn.addEventListener('click', () => {
            initializeModals();
            setupConfirmationFlow();
            
            const totalAmount = document.getElementById('cart-total-amount').textContent;
            document.getElementById('paymentOrderSummary').textContent = `Total: ${totalAmount}`;
            
            if (paymentModal) {
                paymentModal.show();
            }
        });
    }

    function setupConfirmationFlow() {
        const confirmPaymentBtn = document.getElementById('confirmPayment');
        const finalConfirmOrderBtn = document.getElementById('finalConfirmOrder');
        
        if (!confirmPaymentBtn || !finalConfirmOrderBtn) return;
        
        // Remove existing event listeners and add new ones
        const newConfirmPaymentBtn = confirmPaymentBtn.cloneNode(true);
        const newFinalConfirmOrderBtn = finalConfirmOrderBtn.cloneNode(true);
        
        confirmPaymentBtn.parentNode.replaceChild(newConfirmPaymentBtn, confirmPaymentBtn);
        finalConfirmOrderBtn.parentNode.replaceChild(newFinalConfirmOrderBtn, finalConfirmOrderBtn);

        newConfirmPaymentBtn.addEventListener('click', handlePaymentConfirmation);
        newFinalConfirmOrderBtn.addEventListener('click', handleFinalConfirmation);
    }

    function handlePaymentConfirmation() {
        const selectedPaymentInput = document.querySelector('input[name="paymentType"]:checked');
        if (!selectedPaymentInput) return;
        
        const selectedPayment = selectedPaymentInput.value;
        const paymentMethod = selectedPayment === 'cash' ? 'Cash' : 'Card';
        const totalAmount = document.getElementById('cart-total-amount').textContent;
        const deliveryType = document.getElementById('selected-delivery-type').textContent;
        const address = customerAddressInput ? customerAddressInput.value : '';
        
        let finalMessage = `Please confirm your order:\n\n• Order Type: ${deliveryType}\n• Payment Method: ${paymentMethod}\n• ${totalAmount}\n`;
        
        if (deliveryType === 'Doorstep Delivery' && address) {
            finalMessage += `• Delivery Address: ${address}\n`;
        }
        
        finalMessage += `\nThis action cannot be undone.`;
        
        document.getElementById('finalConfirmationMessage').textContent = finalMessage;
        
        if (paymentModal) paymentModal.hide();
        if (finalConfirmationModal) finalConfirmationModal.show();
    }

    function handleFinalConfirmation() {
        const selectedPaymentInput = document.querySelector('input[name="paymentType"]:checked');
        const selectedPayment = selectedPaymentInput ? selectedPaymentInput.value : 'cash';
        sendOrderData(selectedPayment);
    }

    function sendOrderData(paymentType) {
        const cartData = [];
        let totalAmount = 0;

        const table_number = tableNumberInput ? tableNumberInput.value : '';
        const customer_contact = customerContactInput ? customerContactInput.value : '';
        const customer_address = customerAddressInput ? customerAddressInput.value : '';

        cart = JSON.parse(localStorage.getItem('cart')) || {};

        for (const foodId in cart) {
            const product = cart[foodId];
            const eachTotalPrice = product.price * product.quantity;

            cartData.push({
                id: foodId,
                image: product.image,
                name: product.name,
                price: product.price,
                deliveryType: product.deliveryType,
                locationId: product.locationId,
                quantity: product.quantity,
                eachTotalPrice: eachTotalPrice.toFixed(2),
            });

            totalAmount = (parseFloat(totalAmount) + parseFloat(eachTotalPrice)).toFixed(2);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/menu/create-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ 
                cartData, 
                totalAmount, 
                table_number, 
                customer_contact,
                customer_address,
                payment_type: paymentType 
            }),
        })
        .then(res => res.ok ? res.json() : Promise.reject('Network error'))
        .then(data => {
            if (finalConfirmationModal) finalConfirmationModal.hide();
            
            if (data['success-message']) {
                sessionStorage.removeItem('temp_address');
                
                const successMessage = document.getElementById('successMessage');
                const successModalEl = document.getElementById('successModal');
                const closeModalBtn = document.getElementById('closeSuccessModal');

                if (successModalEl && successMessage) {
                    successMessage.textContent = data['success-message'];

                    const bsModal = new bootstrap.Modal(successModalEl);
                    bsModal.show();

                    if (closeModalBtn) {
                        const newCloseBtn = closeModalBtn.cloneNode(true);
                        closeModalBtn.parentNode.replaceChild(newCloseBtn, closeModalBtn);
                        
                        newCloseBtn.addEventListener('click', () => {
                            localStorage.removeItem('cart');
                            sessionStorage.removeItem('temp_address');
                            updateCart();
                            location.reload();
                        });
                    }
                }
            } else if (data['validation-error-message']) {
                showToast(data['validation-error-message'], 'danger');
            }
        })
        .catch(error => {
            if (finalConfirmationModal) finalConfirmationModal.hide();
            console.error('Error:', error);
            showToast('An error occurred while placing your order. Please try again.', 'danger');
        });
    }
});