/*
*  ------------------- MAIN SCRIPT ----------------------
*/
document.addEventListener('DOMContentLoaded', function() {
    // ================== SCROLLING TOP BAR ==================
    const topbar = document.querySelector('.topbar');
    if (topbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 0) {
                topbar.classList.add('scrolled');
            } else {
                topbar.classList.remove('scrolled');
            }
        });
    }

    // ================== INITIAL DELIVERY OPTION SELECTION ==================
    const deliveryModal = document.getElementById('deliveryOptionModal');
    const menuSection = document.getElementById('menuSection');
    
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

            const selectedOption = sessionStorage.getItem('delivery_type');
            const selectedLocationId = sessionStorage.getItem('location_id');
            
            if (selectedOption) {
                document.querySelectorAll('.add-to-cart').forEach(btn => {
                    btn.dataset.deliveryType = selectedOption;
                    btn.dataset.locationId = selectedLocationId;
                });
            }
        }

        document.querySelectorAll('.delivery-option-card').forEach(button => {
            button.addEventListener('click', function() {
                const selectedOption = this.dataset.option;
                const selectedLocationId = this.dataset.locationId;

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

    // ================== CART MANAGEMENT ==================
    const cartList = document.querySelector('.cart-list');
    const addProduct = document.querySelectorAll('.add-to-cart');
    const tableNumberInput = document.querySelector('input[name="table_number"]');
    const customerContactInput = document.querySelector('input[name="customer_contact"]');
    const additionalContactInput = document.querySelector('input[name="additional_contact"]');
    const customerAddressInput = document.querySelector('textarea[name="customer_address"]');
    const confirmOrderBtn = document.querySelector('.confirm-order');
    const cartBadge = document.getElementById('cart-quantity');

    // Initialize cart
    loadCustomerAddress();
    loadCart();

    // Event Listeners for form inputs
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

    // ================== ADD TO CART ==================
    addProduct.forEach(button => {
        button.addEventListener('click', () => {
            const foodId = button.getAttribute('data-food-id');
            const foodName = button.getAttribute('data-food-name');
            const deliveryType = button.getAttribute('data-delivery-type') || sessionStorage.getItem('delivery_type') || '';
            const locationId = button.getAttribute('data-location-id') || sessionStorage.getItem('location_id') || '';

            if (!locationId) {
                showToast('⚠️ Location ID is missing. Please select a location first.', 'warning');
                return;
            }
            if (!deliveryType) {
                showToast('⚠️ Please select a delivery type before adding to cart!', 'warning');
                return;
            }

            // Add to database via AJAX
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    food_id: foodId,
                    quantity: 1,
                    delivery_type: deliveryType,
                    location_id: locationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(`✓ ${foodName} added to cart!`, 'success');
                    loadCart();
                } else {
                    showToast(data.message, 'warning');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding item to cart', 'danger');
            });
        });
    });

    // ================== QUANTITY CONTROLS ==================
    document.addEventListener('click', (event) => {
        // Minus button
        if (event.target.classList.contains('minus-btn') || event.target.closest('.minus-btn')) {
            const button = event.target.classList.contains('minus-btn') ? event.target : event.target.closest('.minus-btn');
            const foodId = button.getAttribute('data-food-id');
            const currentQty = parseInt(button.nextElementSibling.textContent);
            
            if (currentQty > 1) {
                updateCartQuantity(foodId, currentQty - 1);
            }
        }

        // Plus button
        if (event.target.classList.contains('plus-btn') || event.target.closest('.plus-btn')) {
            const button = event.target.classList.contains('plus-btn') ? event.target : event.target.closest('.plus-btn');
            const foodId = button.getAttribute('data-food-id');
            const currentQty = parseInt(button.previousElementSibling.textContent);
            
            updateCartQuantity(foodId, currentQty + 1);
        }

        // Delete button
        if (event.target.closest('.delete-btn')) {
            const foodId = event.target.closest('.delete-btn').getAttribute('data-food-id');
            removeFromCart(foodId);
        }
    });

    // ================== CHANGE DELIVERY TYPE FUNCTIONALITY ==================
    const changeDeliveryBtn = document.getElementById('changeDeliveryType');
    const changeDeliveryModal = document.getElementById('changeDeliveryModal');
    
    if (changeDeliveryBtn && changeDeliveryModal) {
        const bsChangeModal = new bootstrap.Modal(changeDeliveryModal);
        
        // Open change delivery modal
        changeDeliveryBtn.addEventListener('click', function() {
            bsChangeModal.show();
        });
        
        // Handle delivery option selection
        document.querySelectorAll('.btn-delivery-option').forEach(button => {
            button.addEventListener('click', function() {
                const newDeliveryType = this.getAttribute('data-option');
                const locationId = sessionStorage.getItem('location_id');
                const customerId = document.body.getAttribute('data-customer-id');
                
                // Update delivery type
                updateDeliveryType(newDeliveryType, locationId, customerId);
                bsChangeModal.hide();
            });
        });
    }

    // Function to update delivery type in cart
    function updateDeliveryType(newDeliveryType, locationId, customerId) {
        // Show loading state
        const changeBtn = document.getElementById('changeDeliveryType');
        if (changeBtn) {
            changeBtn.disabled = true;
            changeBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';
        }

        // Update delivery type via AJAX
        fetch('/cart/update-delivery-type', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                delivery_type: newDeliveryType,
                location_id: locationId,
                customer_id: customerId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(`✓ Order type changed to ${newDeliveryType}`, 'success');
                
                // Update session storage FIRST
                sessionStorage.setItem('delivery_type', newDeliveryType);
                
                // Update all add-to-cart buttons
                document.querySelectorAll('.add-to-cart').forEach(btn => {
                    btn.dataset.deliveryType = newDeliveryType;
                });
                
                // Update the display immediately
                const deliveryTypeSpan = document.getElementById('selected-delivery-type');
                if (deliveryTypeSpan) {
                    deliveryTypeSpan.textContent = newDeliveryType;
                }
                
                // Update the delivery info section visibility
                const deliveryInfoSection = document.getElementById('deliveryInfoSection');
                if (deliveryInfoSection) {
                    deliveryInfoSection.classList.remove('d-none');
                }
                
                // Update form sections based on delivery type
                updateDeliveryTypeDisplay(newDeliveryType);
                
                // Clear and reset form fields based on delivery type
                if (newDeliveryType === 'Doorstep Delivery') {
                    if (tableNumberInput) tableNumberInput.value = '';
                    // Load saved address if available
                    loadCustomerAddress();
                } else if (newDeliveryType === 'Restaurant Dine-in') {
                    if (customerAddressInput) customerAddressInput.value = '';
                } else {
                    // Counter Pickup
                    if (tableNumberInput) tableNumberInput.value = '';
                    if (customerAddressInput) customerAddressInput.value = '';
                }
                
                // Reload cart to reflect changes
                loadCart();
                
                // Re-enable button
                if (changeBtn) {
                    changeBtn.disabled = false;
                    changeBtn.innerHTML = '<i class="bx bx-edit"></i> Change';
                }
            } else {
                showToast(data.message || 'Failed to update delivery type', 'danger');
                
                // Re-enable button
                if (changeBtn) {
                    changeBtn.disabled = false;
                    changeBtn.innerHTML = '<i class="bx bx-edit"></i> Change';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error changing delivery type', 'danger');
            
            // Re-enable button
            if (changeBtn) {
                changeBtn.disabled = false;
                changeBtn.innerHTML = '<i class="bx bx-edit"></i> Change';
            }
        });
    }

    // ================== CART FUNCTIONS ==================
    function loadCart() {
        fetch('/cart/get', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCart(data.cart_items, data.total_amount, data.cart_count, data.delivery_type, data.currency);
                
                // Update session storage with current delivery type
                if (data.delivery_type) {
                    sessionStorage.setItem('delivery_type', data.delivery_type);
                }
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
        });
    }

    function displayCart(cartItems, totalAmount, cartCount, deliveryType, currency) {
        if (!cartList) return;
        
        cartList.innerHTML = '';

        if (cartItems.length === 0) {
            cartList.innerHTML = `
                <li class="empty-cart">
                    <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">No items in cart</p>
                </li>
            `;
        } else {
            cartItems.forEach(item => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    <div class="cart-item">
                        <div class="card-body p-3">
                            <div class="row g-3 align-items-center">                               
                                <div class="col">
                                    <h6 class="mb-1 fw-semibold">${item.name}</h6>
                                    <small class="text-muted d-block mb-2">
                                        ${item.currency} ${parseFloat(item.price).toFixed(2)} each
                                    </small>
                                    <div class="fw-bold text-primary">
                                        ${item.currency} ${parseFloat(item.total).toFixed(2)}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-2 mt-2">
                                <div class="col-7">
                                    <div class="qty-controls">
                                        <button type="button" 
                                                class="btn qty-btn minus-btn" 
                                                data-food-id="${item.id}">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="fw-bold mx-2">${item.quantity}</span>
                                        <button type="button" 
                                                class="btn qty-btn plus-btn" 
                                                data-food-id="${item.id}">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <button type="button" 
                                            class="btn btn-danger w-100 btn-sm btn-delete delete-btn" 
                                            data-food-id="${item.id}">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                cartList.appendChild(listItem);
            });
        }

        // Update Summary
        const cartTotalAmount = document.getElementById('cart-total-amount');
        const cartItemCount = document.getElementById('cart-item-count');
        
        if (cartTotalAmount) {
            cartTotalAmount.textContent = `${currency} ${parseFloat(totalAmount).toFixed(2)}`;
        }
        if (cartItemCount) {
            cartItemCount.textContent = `${cartCount} item${cartCount !== 1 ? 's' : ''}`;
        }
        
        // Update Both Cart Badges (Desktop + Mobile)
        const cartBadges = document.querySelectorAll('#cart-quantity, #cart-quantity-mobile');
        
        cartBadges.forEach(badge => {
            if (cartCount > 0) {
                badge.textContent = cartCount;
                badge.style.display = 'inline-flex';
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
        });

        // Update Delivery Type Display
        updateDeliveryTypeDisplay(deliveryType);
        updateConfirmButtonState();
    }

    function updateDeliveryTypeDisplay(deliveryType) {
        const deliveryInfoSection = document.getElementById('deliveryInfoSection');
        const deliveryTypeSpan = document.getElementById('selected-delivery-type');
        const tableNumberSection = document.getElementById('tableNumberSection');
        const addressSection = document.getElementById('addressSection');
        const cashNote = document.getElementById('cashNote');

        if (deliveryTypeSpan) {
            deliveryTypeSpan.textContent = deliveryType || '';
        }

        if (deliveryInfoSection) {
            if (deliveryType) {
                deliveryInfoSection.classList.remove('d-none');
            } else {
                deliveryInfoSection.classList.add('d-none');
            }
        }

        // Show/hide sections based on delivery type
        if (deliveryType === 'Doorstep Delivery') {
            if (tableNumberSection) tableNumberSection.classList.add('d-none');
            if (addressSection) addressSection.classList.remove('d-none');
            if (cashNote) cashNote.classList.remove('d-none');
        } else if (deliveryType === 'Restaurant Dine-in') {
            if (tableNumberSection) tableNumberSection.classList.remove('d-none');
            if (addressSection) addressSection.classList.add('d-none');
            if (cashNote) cashNote.classList.remove('d-none');
        } else {
            // Counter Pickup
            if (tableNumberSection) tableNumberSection.classList.add('d-none');
            if (addressSection) addressSection.classList.add('d-none');
            if (cashNote) cashNote.classList.remove('d-none');
        }
    }

    function updateCartQuantity(foodId, quantity) {
        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                food_id: foodId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCart();
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating cart', 'danger');
        });
    }

    function removeFromCart(foodId) {
        fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                food_id: foodId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'warning');
                loadCart();
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error removing item', 'danger');
        });
    }

    function updateConfirmButtonState() {
        if (!confirmOrderBtn) return;

        const tableNumberValue = tableNumberInput ? tableNumberInput.value.trim() : '';
        const hasContact = customerContactInput ? customerContactInput.value.trim() !== '' : false;
        const addressValue = customerAddressInput ? customerAddressInput.value.trim() : '';

        // Check cart via badge or make an API call
        const cartCount = cartBadge ? parseInt(cartBadge.textContent) || 0 : 0;
        const isCartEmpty = cartCount === 0;

        const deliveryTypeSpan = document.getElementById('selected-delivery-type');
        const deliveryType = deliveryTypeSpan ? deliveryTypeSpan.textContent : '';
        
        const requiresTable = deliveryType === 'Restaurant Dine-in';
        const requiresAddress = deliveryType === 'Doorstep Delivery';

        const isEnabled = !isCartEmpty && 
                          hasContact && 
                          deliveryType !== '' &&
                          (!requiresTable || tableNumberValue !== '') && 
                          (!requiresAddress || addressValue !== '');

        confirmOrderBtn.disabled = !isEnabled;
    }

    function loadCustomerAddress() {
        const customerId = document.body.getAttribute('data-customer-id');
        if (customerId && customerId.trim() !== '') {
            fetch(`/customer/address/${customerId}`)
                .then(response => response.ok ? response.json() : Promise.reject())
                .then(data => {
                    if (data.address && data.address.trim() !== '' && customerAddressInput) {
                        customerAddressInput.value = data.address;
                    } else {
                        const tempAddress = sessionStorage.getItem('temp_address');
                        if (tempAddress && customerAddressInput) {
                            customerAddressInput.value = tempAddress;
                        }
                    }
                    
                    if (data.contact && data.contact.trim() !== '' && customerContactInput) {
                        customerContactInput.value = data.contact;
                    } else {
                        const tempContact = sessionStorage.getItem('temp_contact');
                        if (tempContact && customerContactInput) {
                            customerContactInput.value = tempContact;
                        }
                    }
                    
                    const tempAdditionalContact = sessionStorage.getItem('temp_additional_contact');
                    if (tempAdditionalContact && additionalContactInput) {
                        additionalContactInput.value = tempAdditionalContact;
                    }
                    
                    updateConfirmButtonState();
                })
                .catch(() => {
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
        }
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
        const table_number = tableNumberInput ? tableNumberInput.value : '';
        const customer_contact = customerContactInput ? customerContactInput.value : '';
        const customer_address = customerAddressInput ? customerAddressInput.value : '';
        const additional_contact = additionalContactInput ? additionalContactInput.value : '';

        const totalAmountElement = document.getElementById('cart-total-amount');
        let totalAmountText = totalAmountElement ? totalAmountElement.textContent : '0.00';
    
        const totalAmount = totalAmountText.replace(/[^\d.]/g, '');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/menu/create-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ 
                table_number, 
                customer_contact,
                customer_address,
                additional_contact,
                payment_type: paymentType,
                total_amount: totalAmount,
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
                            sessionStorage.removeItem('temp_address');
                            sessionStorage.removeItem('temp_additional_contact');
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

    // ================== UTILITY FUNCTIONS ==================
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
});