/*
*  ------------------- Scrolling Top Bar ----------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const topbar = document.querySelector('.topbar');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 0) {
            topbar.classList.add('scrolled');
        }
        else {
            topbar.classList.remove('scrolled');
        }
    });
});

/*
*  -------------------- Function for Success Message ---------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const successMessage = document.querySelector('.success-message');

    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            successMessage.remove();
        }, 3000);
    }
});

/*
*  ------------------------------ Search -------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const openSearch = document.getElementById('open-search');
    const searchBox = document.querySelector('.search-container');
    const body = document.querySelector('body');

    openSearch.addEventListener('click', (event) => {
        event.stopPropagation();
        searchBox.classList.toggle('active');
    });

    searchBox.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    body.addEventListener('click', () => {
        searchBox.classList.remove('active');
    });
});

/*
*  -------------------------- Function to Click an Icon for Search ---------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const searchBtn = document.getElementById('search-button');

    searchBtn.addEventListener('click', () => {
        const search = document.getElementById('search-form');
        search.submit();
    });
});

/*
*  ---------------------------- choose delivery option ------------------------------
*/
document.addEventListener('DOMContentLoaded', function() {
    const deliveryModal = new bootstrap.Modal(document.getElementById('deliveryOptionModal'));
    const menuSection = document.getElementById('menuSection');
    let selectedOption = null;
    let selectedLocationId = null;
    
    const navEntries = performance.getEntriesByType("navigation");
    const isReload = navEntries.length > 0 && navEntries[0].type === "reload";

    // Show modal only if not a reload
    if (!isReload) {
        deliveryModal.show();
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

    // Handle delivery type button click
    document.querySelectorAll('.delivery-option-btn').forEach(button => {
        button.addEventListener('click', function() {
            selectedOption = this.dataset.option;
            selectedLocationId = this.dataset.locationId;

            sessionStorage.setItem('delivery_type', selectedOption);
            sessionStorage.setItem('location_id', selectedLocationId);
            
            deliveryModal.hide();
            if (menuSection) {
                menuSection.style.display = 'block';
            }

            document.querySelectorAll('.add-to-cart').forEach(btn => {
                btn.dataset.deliveryType = selectedOption;
                btn.dataset.locationId = selectedLocationId;
            });
        });
    });
});

/*
*  ---------------------------- Add to Cart ------------------------------
*/
document.addEventListener('DOMContentLoaded', (event) => {
    const body = document.querySelector('body');
    const cartSection = document.querySelector('.cart-section');
    const openCart = document.querySelector('.cart');
    const closeCart = document.querySelector('.close-cart');
    const cartList = document.querySelector('.cart-list');
    const addProduct = document.querySelectorAll('.add-to-cart');

    // Handle delivery type selection
    document.querySelectorAll('.delivery-option-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedType = btn.getAttribute('data-option');
            const locationId = btn.getAttribute('data-location-id');

            localStorage.setItem('selectedDeliveryType', selectedType);
            localStorage.setItem('location_id', locationId);

            document.querySelectorAll('.add-to-cart').forEach(addBtn => {
                addBtn.setAttribute('data-delivery-type', selectedType);
                addBtn.setAttribute('data-location-id', locationId);
            });
        });
    });

    // Initialize an empty cart object
    const cart = JSON.parse(localStorage.getItem('cart')) || {};

    // Get inputs
    const tableNumberInput = document.querySelector('input[name="table_number"]');
    const customerContactInput = document.querySelector('input[name="customer_contact"]');
    const confirmOrderBtn = document.querySelector('.confirm-order');

    updateCart();
    updateConfirmButtonState();

    openCart.addEventListener('click', (event) => {
        event.stopPropagation();
        body.classList.add('cart-active');
    });

    closeCart.addEventListener('click', () => {
        body.classList.remove('cart-active');
    });

    body.addEventListener('click', (event) => {
        if (!cartSection.contains(event.target) && !event.target.classList.contains(openCart)) {
            body.classList.remove('cart-active');
        }
    });

    // Add product when clicked
    addProduct.forEach(button => {
        button.addEventListener('click', () => {
            const foodId = button.getAttribute('data-food-id');
            const foodImage = button.getAttribute('data-food-image');
            const foodName = button.getAttribute('data-food-name');
            const foodPrice = button.getAttribute('data-food-price');
            const deliveryType = button.getAttribute('data-delivery-type') || localStorage.getItem('selectedDeliveryType') || '';
            const locationId = button.getAttribute('data-location-id') || localStorage.getItem('location_id') || '';

            // Validate location ID and delivery type
            if (!locationId) {
                alert("⚠️ Location ID is missing. Please select a location first.");
                return;
            }
            if (!deliveryType) {
                alert("⚠️ Please select a delivery type before adding to cart!");
                return;
            }

            const firstCartItemKey = Object.keys(cart)[0];
            const cartDeliveryType = firstCartItemKey ? cart[firstCartItemKey].deliveryType : null;
            const cartLocationId = firstCartItemKey ? cart[firstCartItemKey].locationId : null;

            // Only check for mismatches if cart is NOT empty
            if (firstCartItemKey) {
                if (cartDeliveryType && cartDeliveryType !== deliveryType) {
                    alert(`⚠️ You already have items in your cart with delivery type "${cartDeliveryType}". Please clear your cart before adding items with "${deliveryType}".`);
                    return;
                }

                if (String(cartLocationId) !== String(locationId)) {
                    alert(`⚠️ You already have items in your cart from a different location. Please clear your cart before adding items from this location.`);
                    return;
                }
            }

            if (cart[foodId]) {
                cart[foodId].quantity++;
            } else {
                cart[foodId] = {
                    image: foodImage,
                    name: foodName,
                    price: foodPrice,
                    deliveryType: deliveryType,
                    locationId: locationId,
                    quantity: 1
                }
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCart();
        });
    });

    // Table number input listener
    tableNumberInput.addEventListener('input', () => {
        updateConfirmButtonState();
    });

    // Contact number input listener
    customerContactInput.addEventListener('input', () => {
        updateConfirmButtonState();
    });

    // Minus button
    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('minus')) {
            const foodId = event.target.getAttribute('data-food-id');
            if (cart[foodId] && cart[foodId].quantity > 1) {
                cart[foodId].quantity--;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }
    });

    // Plus button
    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('plus')) {
            const foodId = event.target.getAttribute('data-food-id');
            if (cart[foodId]) {
                cart[foodId].quantity++;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }
    });

    // Delete button
    document.addEventListener('click', (event) => {
        if (event.target.classList.contains('bx') || event.target.classList.contains('bxs-trash')) {
            const foodId = event.target.getAttribute('data-food-id');
            if (cart[foodId]) {
                delete cart[foodId];
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
                updateConfirmButtonState();
            }
        }
    });

    // ✅ Delivery Type Change Functionality
    document.getElementById('changeDeliveryType').addEventListener('click', () => {
        const changeDeliveryModal = new bootstrap.Modal(document.getElementById('changeDeliveryModal'));
        changeDeliveryModal.show();
    });

    // Handle delivery type change in modal
    document.querySelectorAll('.btn-delivery-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const newDeliveryType = this.getAttribute('data-option');
            const locationId = localStorage.getItem('location_id');
            
            // Update all items in cart with new delivery type
            for (const foodId in cart) {
                cart[foodId].deliveryType = newDeliveryType;
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            localStorage.setItem('selectedDeliveryType', newDeliveryType);
            
            document.querySelectorAll('.add-to-cart').forEach(addBtn => {
                addBtn.setAttribute('data-delivery-type', newDeliveryType);
            });
            
            updateCart();
            bootstrap.Modal.getInstance(document.getElementById('changeDeliveryModal')).hide();
            showTempMessage(`Order type changed to ${newDeliveryType}`, 'success');
        });
    });

    // Update cart view
    function updateCart() {
        cartList.innerHTML = '';

        let totalAmount = 0;
        let totalItemCount = 0;

        if (Object.keys(cart).length === 0) {
            cartList.innerHTML = '<li><span class="empty">No item in cart</span></li>';
        } else {
            for (const foodId in cart) {
                const product = cart[foodId];
                const listItem = document.createElement('li');
                const productTotalPrice = product.price * product.quantity;

                listItem.innerHTML = `
                    <div class="product">
                        <img src="${product.image}" alt="food-image">
                        <span>${product.name}</span>
                    </div>
                    <div class="quantity-price">
                        <span>${product.quantity}</span>
                        <span>RM ${(productTotalPrice).toFixed(2)}</span>
                    </div>
                    <div class="action">
                        <button type="button" class="minus" data-food-id="${foodId}">-</button>
                        <span>${product.quantity}</span>
                        <button type="button" class="plus" data-food-id="${foodId}">+</button>
                    </div>
                    <div class="delete">
                        <button type="button" class="cart-list-delete">
                            <i class='bx bxs-trash' data-food-id="${foodId}"></i>
                        </button>
                    </div>
                `;

                cartList.appendChild(listItem);
                totalAmount += productTotalPrice;
                totalItemCount += product.quantity;
            }
        }

        document.getElementById('cart-total-amount').textContent = `RM ${totalAmount.toFixed(2)}`;
        document.getElementById('cart-item-count').textContent = `Total ${totalItemCount} items`;
        document.getElementById('cart-quantity').textContent = totalItemCount;

        // ✅ Show delivery type and table number visibility
        const deliveryInfoDiv = document.querySelector('.delivery-type-info');
        const deliveryTypeSpan = document.getElementById('selected-delivery-type');
        const tableNumberSection = document.querySelector('.table-number');

        if (Object.keys(cart).length > 0) {
            const firstItem = cart[Object.keys(cart)[0]];
            const deliveryType = firstItem.deliveryType || '';

            if (deliveryTypeSpan) deliveryTypeSpan.textContent = deliveryType;
            if (deliveryInfoDiv) deliveryInfoDiv.style.display = 'block';

            // Show table number only for Restaurant Dine-in
            if (deliveryType === 'Restaurant Dine-in') {
                tableNumberSection.style.display = 'block';
            } else {
                tableNumberSection.style.display = 'none';
                tableNumberInput.value = ''; // clear old value
            }
        } else {
            if (deliveryInfoDiv) deliveryInfoDiv.style.display = 'none';
            tableNumberSection.style.display = 'none';
        }

        updateConfirmButtonState();
    }

    // ✅ Button enable/disable logic
    function updateConfirmButtonState() {
        const tableNumberValue = tableNumberInput.value.trim();
        const isCartEmpty = Object.keys(cart).length === 0;
        const hasContact = customerContactInput.value.trim() !== '';

        let requiresTable = false;
        let hasDeliveryType = false;

        if (Object.keys(cart).length > 0) {
            const firstItem = cart[Object.keys(cart)[0]];
            if (firstItem.deliveryType) {
                hasDeliveryType = true;
                // Only require table number for Restaurant Dine-in
                requiresTable = firstItem.deliveryType === 'Restaurant Dine-in';
            }
        }

        if (!isCartEmpty && hasContact && hasDeliveryType && (!requiresTable || tableNumberValue !== '')) {
            confirmOrderBtn.disabled = false;
        } else {
            confirmOrderBtn.disabled = true;
        }
    }

    // ✅ Helper function to show temporary messages
    function showTempMessage(message, type) {
        const tempDiv = document.createElement('div');
        tempDiv.className = `alert alert-${type} alert-dismissible fade show`;
        tempDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.cart-section').insertBefore(tempDiv, document.querySelector('.cart-section').firstChild);
        
        setTimeout(() => {
            if (tempDiv.parentNode) {
                tempDiv.remove();
            }
        }, 3000);
    }

    // ✅ Payment and Order Confirmation Flow
    let paymentModal, finalConfirmationModal;

    function initializeModals() {
        if (!paymentModal) {
            paymentModal = new bootstrap.Modal(document.getElementById('paymentConfirmationModal'));
        }
        if (!finalConfirmationModal) {
            finalConfirmationModal = new bootstrap.Modal(document.getElementById('finalConfirmationModal'));
        }
    }

    function setupConfirmationFlow() {
        const confirmPaymentBtn = document.getElementById('confirmPayment');
        const finalConfirmOrderBtn = document.getElementById('finalConfirmOrder');
        
        const newConfirmPaymentBtn = confirmPaymentBtn.cloneNode(true);
        const newFinalConfirmOrderBtn = finalConfirmOrderBtn.cloneNode(true);
        
        confirmPaymentBtn.parentNode.replaceChild(newConfirmPaymentBtn, confirmPaymentBtn);
        finalConfirmOrderBtn.parentNode.replaceChild(newFinalConfirmOrderBtn, finalConfirmOrderBtn);

        newConfirmPaymentBtn.addEventListener('click', handlePaymentConfirmation);
        newFinalConfirmOrderBtn.addEventListener('click', handleFinalConfirmation);
    }

    function handlePaymentConfirmation() {
        const selectedPayment = document.querySelector('input[name="paymentType"]:checked').value;
        const paymentMethod = selectedPayment === 'cash' ? 'Cash' : 'Card';
        const totalAmount = document.getElementById('cart-total-amount').textContent;
        
        const finalMessage = `Please confirm your order:\n\n• Order Type: ${document.getElementById('selected-delivery-type').textContent}\n• Payment Method: ${paymentMethod}\n• ${totalAmount}\n\nThis action cannot be undone.`;
        document.getElementById('finalConfirmationMessage').textContent = finalMessage;
        
        paymentModal.hide();
        finalConfirmationModal.show();
    }

    function handleFinalConfirmation() {
        const selectedPayment = document.querySelector('input[name="paymentType"]:checked').value;
        sendOrderData(selectedPayment);
    }

    confirmOrderBtn.addEventListener('click', () => {
        // First check if delivery type needs to be changed
        const firstCartItemKey = Object.keys(cart)[0];
        const currentDeliveryType = firstCartItemKey ? cart[firstCartItemKey].deliveryType : null;
        const selectedDeliveryType = localStorage.getItem('selectedDeliveryType');
        
        if (currentDeliveryType && selectedDeliveryType && currentDeliveryType !== selectedDeliveryType) {
            if (confirm(`You have items in your cart with "${currentDeliveryType}" order type, but currently selected is "${selectedDeliveryType}". Do you want to change the order type for all items?`)) {
                for (const foodId in cart) {
                    cart[foodId].deliveryType = selectedDeliveryType;
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
            }
        }

        initializeModals();
        setupConfirmationFlow();
        
        const totalAmount = document.getElementById('cart-total-amount').textContent;
        document.getElementById('paymentOrderSummary').textContent = `Total: ${totalAmount}`;
        
        paymentModal.show();
    });

    // ✅ Send order data function
    function sendOrderData(paymentType) {
        const cartData = [];
        let totalAmount = 0;

        const table_number = tableNumberInput.value;
        const customer_contact = customerContactInput.value;

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

        const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;

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
                payment_type: paymentType 
            }),
        })
        .then(res => res.json())
        .then(data => {
            finalConfirmationModal.hide();
            
            const successMessage = document.getElementById('successMessage');
            const successModalEl = document.getElementById('successModal');
            const closeModalBtn = document.getElementById('closeSuccessModal');

            if (data['success-message'] && successModalEl && successMessage) {
                successMessage.textContent = data['success-message'];

                const bsModal = new bootstrap.Modal(successModalEl);
                bsModal.show();

                const newCloseBtn = closeModalBtn.cloneNode(true);
                closeModalBtn.parentNode.replaceChild(newCloseBtn, closeModalBtn);
                
                newCloseBtn.addEventListener('click', () => {
                    localStorage.clear();
                    updateCart();
                    location.reload();
                });
            } else if (data['validation-error-message']) {
                const error = document.getElementById('error-response');
                error.textContent = data['validation-error-message'];
                error.classList.remove('success');
                error.classList.add('error');
            }
        })
        .catch(error => {
            finalConfirmationModal.hide();
            console.error('Error:', error);
            alert('An error occurred while placing your order. Please try again.');
        });
    }
});