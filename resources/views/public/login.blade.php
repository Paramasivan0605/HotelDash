<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MadrasDarbar - Order</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1920') center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .order-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .logo {
            background: #8B0000;
            color: white;
            text-align: center;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 35px;
        }

        .logo-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .logo h1 {
            font-size: 26px;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .logo p {
            font-size: 11px;
            letter-spacing: 3px;
            opacity: 0.9;
        }

        .order-type {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .type-option {
            flex: 1;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-align: center;
            min-height: 120px;
            justify-content: center;
        }

        .type-option:hover {
            border-color: #8B0000;
            transform: translateY(-2px);
        }

        .type-option.active {
            border-color: #8B0000;
            background: #fff5f5;
        }

        .type-option input[type="radio"] {
            display: none;
        }

        .type-icon {
            font-size: 32px;
        }

        .type-label {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .phone-section {
            margin-bottom: 25px;
        }

        .phone-section h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }

        .phone-input {
            width: 100%;
            padding: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            background-color: white;
            transition: all 0.3s;
        }

        .phone-input:focus {
            outline: none;
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        }

        .phone-input:hover {
            border-color: #8B0000;
        }

        .phone-input::placeholder {
            color: #999;
        }

        .location-section {
            margin-bottom: 25px;
        }

        .location-section h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 16px;
        }

        .location-select {
            width: 100%;
            padding: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }

        .location-select:focus {
            outline: none;
            border-color: #8B0000;
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        }

        .location-select:hover {
            border-color: #8B0000;
        }

        .location-option {
            padding: 12px 16px;
            font-size: 15px;
        }

        .location-option:disabled {
            color: #999;
            font-style: italic;
        }

        .continue-button {
            width: 100%;
            padding: 18px;
            background: #8B0000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .continue-button:hover {
            background: #6B0000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
        }

        .continue-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .message {
            margin-top: 15px;
            padding: 14px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            text-align: center;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }

        .message.success {
            background: #efe;
            color: #070;
            border: 1px solid #cfc;
        }

        .location-info {
            margin-top: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #495057;
            display: none;
            border-left: 4px solid #8B0000;
        }

        .location-info.show {
            display: block;
        }

        .phone-info {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .phone-info i {
            color: #8B0000;
        }

        .loader {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #8B0000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Phone input specific styles */
        .phone-input-container {
            position: relative;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .order-container {
                padding: 25px;
            }
            
            .logo {
                padding: 20px;
                margin-bottom: 25px;
            }
            
            .logo h1 {
                font-size: 22px;
            }
            
            .order-type {
                flex-direction: row;
                gap: 10px;
            }
            
            .type-option {
                padding: 15px;
                min-height: 100px;
            }
            
            .type-icon {
                font-size: 28px;
            }
            
            .type-label {
                font-size: 14px;
            }
        }

        /* For very small screens */
        @media (max-width: 360px) {
            .order-type {
                gap: 8px;
            }
            
            .type-option {
                padding: 12px;
                min-height: 90px;
            }
            
            .type-icon {
                font-size: 24px;
            }
            
            .type-label {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="order-container">
        <div class="logo">
            <div class="logo-icon">🍽️</div>
            <h1>MadrasDarbar</h1>
            <p>HEARTY DINING</p>
        </div>

        <form id="orderForm">
            @csrf
            
            <div class="order-type">
                <label class="type-option active" for="delivery">
                    <input type="radio" name="order_type" id="delivery" value="delivery" checked>
                    <div class="type-icon">🚚</div>
                    <div class="type-label">Delivery</div>
                </label>
                <label class="type-option" for="pickup">
                    <input type="radio" name="order_type" id="pickup" value="pickup">
                    <div class="type-icon">🏪</div>
                    <div class="type-label">Pickup</div>
                </label>
            </div>

            <div class="phone-section">
                <h3>Enter Your Phone Number</h3>
                <div class="phone-input-container">
                    <input 
                        type="tel" 
                        name="phone_number" 
                        id="phoneInput" 
                        class="phone-input" 
                        placeholder="Enter your mobile number"
                        required
                    >
                </div>
                <div class="phone-info">
                    <i>📞</i>
                    <span>We'll use this to contact you about your order</span>
                </div>
            </div>

            <div class="location-section">
                <h3>Select Your Location</h3>
                <select name="location_id" id="locationSelect" class="location-select" required>
                    <option value="" disabled selected>Choose a location...</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->location_id }}" class="location-option">
                            {{ $location->location_name }} 
                        </option>
                    @endforeach
                </select>
                <div class="location-info" id="locationInfo">
                    <strong>Selected Location:</strong>
                    <span id="selectedLocationText"></span>
                </div>
            </div>

            <button type="submit" class="continue-button" id="continueBtn">
                Continue to Menu
            </button>
            <div class="message" id="message"></div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderForm = document.getElementById('orderForm');
            const phoneInput = document.getElementById('phoneInput');
            const locationSelect = document.getElementById('locationSelect');
            const locationInfo = document.getElementById('locationInfo');
            const selectedLocationText = document.getElementById('selectedLocationText');
            const messageDiv = document.getElementById('message');
            const continueBtn = document.getElementById('continueBtn');

            // Order type selection
            document.querySelectorAll('.type-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.type-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    this.classList.add('active');
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });

            // Location selection change
            locationSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    selectedLocationText.textContent = selectedOption.text;
                    locationInfo.classList.add('show');
                } else {
                    locationInfo.classList.remove('show');
                }
                validateForm();
            });

            // Form validation
            function validateForm() {
                const locationValid = locationSelect.value !== '';
                
                continueBtn.disabled = !(locationValid);
            }

            // Form submission
            orderForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const orderType = document.querySelector('input[name="order_type"]:checked').value;
                const phoneNumber = phoneInput.value;
                const locationId = locationSelect.value;

                if (!phoneNumber) {
                    showMessage('Please enter a phone number', 'error');
                    return;
                }

                if (!locationId) {
                    showMessage('Please select a location', 'error');
                    return;
                }

                // Show loading state
                continueBtn.disabled = true;
                continueBtn.innerHTML = '<div class="loader"></div> Processing...';

                try {
                    const response = await fetch('/order/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            order_type: orderType,
                            location_id: locationId,
                            phone_number: phoneNumber
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage('Phone number and location saved successfully! Redirecting...', 'success');
                        // Redirect to menu page
                        setTimeout(() => {
                            window.location.href = '/home';
                        }, 1000);
                    } else {
                        showMessage(data.message || 'An error occurred', 'error');
                    }
                } catch (error) {
                    showMessage('Network error. Please try again.', 'error');
                    console.error('Submission error:', error);
                } finally {
                    continueBtn.disabled = false;
                    continueBtn.innerHTML = 'Continue to Menu';
                }
            });

            function showMessage(text, type) {
                messageDiv.textContent = text;
                messageDiv.className = `message ${type} show`;
                
                setTimeout(() => {
                    messageDiv.classList.remove('show');
                }, 5000);
            }

            // Auto-select first location if only one exists
            if (locationSelect.options.length === 2) { // 1 option + default option
                locationSelect.selectedIndex = 1;
                locationSelect.dispatchEvent(new Event('change'));
            }

            // Initial form validation
            validateForm();
        });
    </script>
</body>
</html>