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
*  ---------------------- End of Scrolling Top Bar ---------------------
*/








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
*  -------------------------- End of Function for Success Message -------------------
*/



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
*  ------------------------------ End of Search -------------------------------------
*/


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
*  ---------------------- End of Function to Click an Icon for Search -----------------------
*/


/*
*  ---------------------------- choose delivery option ------------------------------
*/
document.addEventListener('DOMContentLoaded', function() {
    const deliveryModal = new bootstrap.Modal(document.getElementById('deliveryOptionModal'));
    const menuSection = document.getElementById('menuSection');
    let selectedOption = null;

    // Show modal when page loads
    deliveryModal.show();

    // Handle delivery type button click
    document.querySelectorAll('.delivery-option-btn').forEach(button => {
        button.addEventListener('click', function() {
            selectedOption = this.dataset.option;

            // Optional: store in localStorage
            localStorage.setItem('delivery_type', selectedOption);

            // Hide modal and show menu
            deliveryModal.hide();
            if (menuSection) {
                menuSection.style.display = 'block';
            }

            // Add delivery type to all add-to-cart buttons
            document.querySelectorAll('.add-to-cart').forEach(btn => {
                btn.dataset.deliveryType = selectedOption;
            });
        });
    });
});


/*
*  ---------------------------- end of delivery option ------------------------------
*/


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

    // ✅ Handle delivery type selection (Restaurant Dine-in / Doorstep Delivery)
    document.querySelectorAll('.delivery-option-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedType = btn.getAttribute('data-option');
            localStorage.setItem('selectedDeliveryType', selectedType);

            // Set this delivery type on all add-to-cart buttons
            document.querySelectorAll('.add-to-cart').forEach(addBtn => {
                addBtn.setAttribute('data-delivery-type', selectedType);
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

    // Click outside cart section will close the cart
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

            if (!deliveryType) {
                alert("⚠️ Please select a delivery type before adding to cart!");
                return;
            }

            if (cart[foodId]) {
                cart[foodId].quantity++;
            } else {
                cart[foodId] = {
                    image: foodImage,
                    name: foodName,
                    price: foodPrice,
                    deliveryType: deliveryType,
                    quantity: 1
                }
            }

            // Save cart
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

    // Update cart view
    function updateCart() {
        cartList.innerHTML = '';

        let totalAmount = 0;
        let totalItemCount = 0;

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
                requiresTable = firstItem.deliveryType === 'Restaurant Dine-in';
            }
        }

        if (!isCartEmpty && hasContact && hasDeliveryType && (!requiresTable || tableNumberValue !== '')) {
            confirmOrderBtn.disabled = false;
        } else {
            confirmOrderBtn.disabled = true;
        }
    }

    // ✅ Send order data
    confirmOrderBtn.addEventListener('click', () => {
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
            body: JSON.stringify({ cartData, totalAmount, table_number, customer_contact }),
        })
        .then(res => res.json())
        .then(data => {
            const successMessage = document.getElementById('successMessage');
            const successModalEl = document.getElementById('successModal');
            const closeModalBtn = document.getElementById('closeSuccessModal');

            if (data['success-message'] && successModalEl && successMessage) {
                successMessage.textContent = data['success-message'];

                // Show modal using Bootstrap 5
                const bsModal = new bootstrap.Modal(successModalEl);
                bsModal.show();

                closeModalBtn.addEventListener('click', () => {
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
    });
});
/*
*  -------------------------- End of Add to Cart -----------------------
*/
