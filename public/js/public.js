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

        document.querySelectorAll('.delivery-option-card').forEach(button => {
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
*  ---------------------------- Add to Cart - MOBILE FIXED ------------------------------
*/
document.addEventListener('DOMContentLoaded', (event) => {
    const cartList = document.querySelector('.cart-list');
    const addProduct = document.querySelectorAll('.add-to-cart');
    let cart = JSON.parse(localStorage.getItem('cart')) || {};

    const tableNumberInput = document.querySelector('input[name="table_number"]');
    const customerContactInput = document.querySelector('input[name="customer_contact"]');
    const additionalContactInput = document.querySelector('input[name="additional_contact"]');
    const customerAddressInput = document.querySelector('textarea[name="customer_address"]');
    const confirmOrderBtn = document.querySelector('.confirm-order');
    const cartBadge = document.getElementById('cart-quantity');

    // Initialize
    loadCustomerAddress();
    updateCart();
    updateConfirmButtonState();

    // Event Listeners
    if (customerContactInput) {
        customerContactInput.addEventListener('input', () => {
            updateConfirmButtonState();
            sessionStorage.setItem('temp_contact', customerContactInput.value);
        });
    }

    if (additionalContactInput) {
        additionalContactInput.addEventListener('input', () => {
            sessionStorage.setItem('temp_additional_contact', additionalContactInput.value);
        });
    }

    if (customerAddressInput) {
        customerAddressInput.addEventListener('input', () => {
            updateConfirmButtonState();
            sessionStorage.setItem('temp_address', customerAddressInput.value);
        });
    }

    if (tableNumberInput) {
        tableNumberInput.addEventListener('input', updateConfirmButtonState);
    }

    // Add to Cart
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

    // Quantity Controls
    document.addEventListener('click', (event) => {
        // Minus button
        if (event.target.classList.contains('minus-btn') || event.target.closest('.minus-btn')) {
            const button = event.target.classList.contains('minus-btn') ? event.target : event.target.closest('.minus-btn');
            const foodId = button.getAttribute('data-food-id');
            cart = JSON.parse(localStorage.getItem('cart')) || {};
            
            if (cart[foodId] && cart[foodId].quantity > 1) {
                cart[foodId].quantity--;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }

        // Plus button
        if (event.target.classList.contains('plus-btn') || event.target.closest('.plus-btn')) {
            const button = event.target.classList.contains('plus-btn') ? event.target : event.target.closest('.plus-btn');
            const foodId = button.getAttribute('data-food-id');
            cart = JSON.parse(localStorage.getItem('cart')) || {};
            
            if (cart[foodId]) {
                cart[foodId].quantity++;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }

        // Delete button
        if (event.target.closest('.delete-btn')) {
            const foodId = event.target.closest('.delete-btn').getAttribute('data-food-id');
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

    // Change Delivery Type
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

    // ================== UPDATE CART DISPLAY - MOBILE OPTIMIZED ==================
    function updateCart() {
        cartList.innerHTML = '';
        cart = JSON.parse(localStorage.getItem('cart')) || {};
        
        let totalAmount = 0;
        let totalItemCount = 0;

        if (Object.keys(cart).length === 0) {
            cartList.innerHTML = `
                <li class="empty-cart">
                    <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">No items in cart</p>
                </li>
            `;
        } else {
            for (const foodId in cart) {
                const product = cart[foodId];
                const productTotalPrice = product.price * product.quantity;

                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    <div class="cart-item">
                        <div class="card-body p-3">
                            <div class="row g-3 align-items-center">
                                <!-- Product Image -->
                                <div class="col-auto">
                                    <img src="${product.image}" 
                                         alt="${product.name}" 
                                         class="cart-item-img">
                                </div>
                                
                                <!-- Product Details -->
                                <div class="col">
                                    <h6 class="mb-1 fw-semibold">${product.name}</h6>
                                    <small class="text-muted d-block mb-2">RM ${product.price.toFixed(2)} each</small>
                                    <div class="fw-bold text-primary">RM ${productTotalPrice.toFixed(2)}</div>
                                </div>
                            </div>
                            
                            <!-- Quantity Controls & Delete -->
                            <div class="row g-2 mt-2">
                                <div class="col-7">
                                    <div class="qty-controls">
                                        <button type="button" 
                                                class="btn qty-btn minus-btn" 
                                                data-food-id="${foodId}">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="fw-bold mx-2">${product.quantity}</span>
                                        <button type="button" 
                                                class="btn qty-btn plus-btn" 
                                                data-food-id="${foodId}">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <button type="button" 
                                            class="btn btn-danger w-100 btn-sm btn-delete delete-btn" 
                                            data-food-id="${foodId}">
                                        <i class="bi bi-trash"></i> Remove
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

        // Update Summary
        document.getElementById('cart-total-amount').textContent = `RM ${totalAmount.toFixed(2)}`;
        document.getElementById('cart-item-count').textContent = `${totalItemCount} item${totalItemCount !== 1 ? 's' : ''}`;
        
        // Update Badge
        if (cartBadge) {
            if (totalItemCount > 0) {
                cartBadge.textContent = totalItemCount;
                cartBadge.style.display = 'inline-flex';
            } else {
                cartBadge.textContent = '0';
                cartBadge.style.display = 'none';
            }
        }

        // Update Delivery Type Display
        const deliveryInfoSection = document.getElementById('deliveryInfoSection');
        const deliveryTypeSpan = document.getElementById('selected-delivery-type');
        const tableNumberSection = document.getElementById('tableNumberSection');
        const addressSection = document.getElementById('addressSection');
        const cashNote = document.getElementById('cashNote');

        if (Object.keys(cart).length > 0) {
            const firstItem = cart[Object.keys(cart)[0]];
            const deliveryType = firstItem.deliveryType || '';

            if (deliveryTypeSpan) deliveryTypeSpan.textContent = deliveryType;
            if (deliveryInfoSection) deliveryInfoSection.classList.remove('d-none');

            if (deliveryType === 'Doorstep Delivery') {
                if (tableNumberSection) tableNumberSection.classList.add('d-none');
                if (addressSection) addressSection.classList.remove('d-none');
                if (cashNote) cashNote.classList.remove('d-none');
                if (tableNumberInput) tableNumberInput.value = '';
            } else if (deliveryType === 'Restaurant Dine-in') {
                if (tableNumberSection) tableNumberSection.classList.remove('d-none');
                if (addressSection) addressSection.classList.add('d-none');
                if (cashNote) cashNote.classList.remove('d-none');
            } else {
                if (tableNumberSection) tableNumberSection.classList.add('d-none');
                if (addressSection) addressSection.classList.add('d-none');
                if (cashNote) cashNote.classList.remove('d-none');
                if (tableNumberInput) tableNumberInput.value = '';
            }
        } else {
            if (deliveryInfoSection) deliveryInfoSection.classList.add('d-none');
            if (tableNumberSection) tableNumberSection.classList.add('d-none');
            if (addressSection) addressSection.classList.add('d-none');
            if (cashNote) cashNote.classList.add('d-none');
        }

        updateConfirmButtonState();
    }

    // Update Confirm Button State
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
    }

    // Load Customer Address and Contact
    function loadCustomerAddress() {
        const customerId = document.body.getAttribute('data-customer-id');
        if (customerId && customerId.trim() !== '') {
            fetch(`/customer/address/${customerId}`)
                .then(response => response.ok ? response.json() : Promise.reject())
                .then(data => {
                    // Pre-fill address
                    if (data.address && data.address.trim() !== '' && customerAddressInput) {
                        customerAddressInput.value = data.address;
                    } else {
                        const tempAddress = sessionStorage.getItem('temp_address');
                        if (tempAddress && customerAddressInput) {
                            customerAddressInput.value = tempAddress;
                        }
                    }
                    
                    // Pre-fill contact
                    if (data.contact && data.contact.trim() !== '' && customerContactInput) {
                        customerContactInput.value = data.contact;
                    } else {
                        const tempContact = sessionStorage.getItem('temp_contact');
                        if (tempContact && customerContactInput) {
                            customerContactInput.value = tempContact;
                        }
                    }
                    
                    // Load additional contact from session if exists
                    const tempAdditionalContact = sessionStorage.getItem('temp_additional_contact');
                    if (tempAdditionalContact && additionalContactInput) {
                        additionalContactInput.value = tempAdditionalContact;
                    }
                    
                    updateConfirmButtonState();
                })
                .catch(() => {
                    // Fallback to session storage
                    const tempAddress = sessionStorage.getItem('temp_address');
                    if (tempAddress && customerAddressInput) {
                        customerAddressInput.value = tempAddress;
                    }
                    
                    const tempContact = sessionStorage.getItem('temp_contact');
                    if (tempContact && customerContactInput) {
                        customerContactInput.value = tempContact;
                    }
                    
                    const tempAdditionalContact = sessionStorage.getItem('temp_additional_contact');
                    if (tempAdditionalContact && additionalContactInput) {
                        additionalContactInput.value = tempAdditionalContact;
                    }
                    
                    updateConfirmButtonState();
                });
        } else {
            // For guest users, load from session storage
            const tempAddress = sessionStorage.getItem('temp_address');
            if (tempAddress && customerAddressInput) {
                customerAddressInput.value = tempAddress;
            }
            
            const tempContact = sessionStorage.getItem('temp_contact');
            if (tempContact && customerContactInput) {
                customerContactInput.value = tempContact;
            }
            
            const tempAdditionalContact = sessionStorage.getItem('temp_additional_contact');
            if (tempAdditionalContact && additionalContactInput) {
                additionalContactInput.value = tempAdditionalContact;
            }
            
            updateConfirmButtonState();
        }
    }

    // Toast Notification
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
        const additionalContact = additionalContactInput ? additionalContactInput.value : '';
        
        let finalMessage = `Please confirm your order:\n\n• Order Type: ${deliveryType}\n• Payment Method: ${paymentMethod}\n• ${totalAmount}\n`;
        
        if (deliveryType === 'Doorstep Delivery' && address) {
            finalMessage += `• Delivery Address: ${address}\n`;
        }
        
        if (additionalContact) {
            finalMessage += `• Additional Contact: ${additionalContact}\n`;
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
        const additional_contact = additionalContactInput ? additionalContactInput.value : '';

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
                additional_contact,
                payment_type: paymentType 
            }),
        })
        .then(res => res.ok ? res.json() : Promise.reject('Network error'))
        .then(data => {
            if (finalConfirmationModal) finalConfirmationModal.hide();
            
            if (data['success-message']) {
                sessionStorage.removeItem('temp_address');
                sessionStorage.removeItem('temp_additional_contact');
                
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
                            sessionStorage.removeItem('temp_additional_contact');
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