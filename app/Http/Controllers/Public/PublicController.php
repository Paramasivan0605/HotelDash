<?php

namespace App\Http\Controllers\Public;

use App\Enums\OrderStatusEnum;
use App\Enums\ReservationStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderDetail;
use App\Models\DiningTable;
use App\Models\FoodCategory;
use App\Models\FoodMenu;
use App\Models\FoodLocation;
use App\Models\Location;
use App\Models\PromotionDiscount;
use App\Models\PromotionEvent;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function login()
    {
        $locations = Location::all(); // Fetch location data
        return view('public.login', compact('locations'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'mobile' => 'required|numeric',
            'location' => 'required|exists:location,location_id', // validate location
        ]);

        if ($validator->fails()) {
            // Uncomment this for debugging if needed
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Find existing customer or create new one
        $customer = Customer::where('mobile', $validated['mobile'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
            ]);
            Log::info('New customer created: ' . $customer->name . ' (ID: ' . $customer->id . ')');
        } else {
            Log::info('Customer logged in: ' . $customer->name . ' (ID: ' . $customer->id . ')');
        }
                // After the customer is found or created
        // If existing customer, restore previous cart and order data
        if ($customer->wasRecentlyCreated === false) {
            // Store old cart info in session (if any exists)
            session(['restore_cart' => true]);

            // Optional: Load the last order status
            $lastOrder = CustomerOrder::where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // If the last order is not completed or cancelled, you can notify them
            if ($lastOrder && !in_array($lastOrder->order_status, ['completed', 'cancelled'])) {
                session(['pending_order' => $lastOrder->id]);
            }
        } else {
            // New customer - fresh start
            session()->forget(['restore_cart', 'pending_order']);
        }


        // Store basic customer data in session
        session([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_mobile' => $customer->mobile,
        ]);

        Log::info('Login submitted for customer: ' . $customer->name . ' (ID: ' . $customer->id . ')');

        // ✅ Redirect the user to that location's menu page
        return redirect()->route('location.menu', ['id' => $validated['location']])
            ->with('success-message', 'Welcome, ' . $customer->name . '!');
    }


    public function home(): View
    {
        $menu = FoodMenu::all();

        return view('public.home', compact('menu'));
    }

    public function menu(): View
    {
        $menu = FoodMenu::all();
        $category = FoodCategory::all();
        $locations = Location::all();

        return view('public.menu', compact('menu', 'category','locations'));
    }

    public function locationMenuPage($locationId)
    {
        // Get the location
        $location = Location::findOrFail($locationId);

        // Join food_locations with food_menu to get food details and price for this location
        $foodMenu = FoodMenu::select('food_menus.*', 'food_price.price')
            ->join('food_price', 'food_price.food_id', '=', 'food_menus.id')
            ->where('food_price.location_id', $locationId)
            ->get();

       return view('public.locationMenu', compact('foodMenu', 'location'))
    ->with([
        'restore_cart' => session('restore_cart'),
        'pending_order' => session('pending_order')
    ]);

    }

    public function about(): View
    {
        return view('public.about');
    }

    public function promotion(): View
    {
        // Get current date
        $currentMonth = Carbon::now();

        // Get month before now
        $monthBefore = Carbon::now()->subMonth(); 

        // Get event available if user is within the event month and one month after even
        // Other than that the promotion is unavailable
        $promotion = PromotionEvent::where(function ($query) use ($currentMonth, $monthBefore) {
            $query->whereMonth('event_date', '=', $currentMonth)
                ->orWhereMonth('event_date', '=', $monthBefore);
        })->get();

        // Initialize as empty array to store coupon based on $promotion
        $coupon = [];

        foreach ($promotion as $event) {
            $eventCoupon = PromotionDiscount::where('event_id', $event->id)->get();
            $coupon[$event->id] = $eventCoupon;
        }

        $menu = FoodMenu::where('price', '>', 27.00)->get();

        return view('public.promotion', compact('promotion', 'coupon', 'menu'));
    }

    public function reservation(): View
    {
        return view('public.reservation');
    }

    // Add new method for order history
    public function orderHistory(): View
    {
        $orders = [];
        if (session('customer_id')) {
            $orders = CustomerOrder::with(['customerOrderDetail.foodMenu.foodLocations', 'diningTable'])
                ->where('customer_id', session('customer_id'))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('public.order-history', compact('orders'));
    }

    // Add method for order details
    public function orderDetails($orderId): View
    {
        $order = CustomerOrder::with(['customerOrderDetail.foodMenu.foodLocations', 'diningTable'])
            ->where('customer_id', session('customer_id'))
            ->where('id', $orderId)
            ->firstOrFail();

        return view('public.order-details', compact('order'));
    }

    
    // Helper method for status classes (add this to your controller)
    public static function getStatusClass($status)
    {
        // Handle both string and OrderStatusEnum cases
        if ($status instanceof \App\Enums\OrderStatusEnum) {
            $statusValue = $status->value;
        } else {
            $statusValue = $status;
        }

        // Normalize the status to handle case inconsistencies
        $normalizedStatus = strtolower($statusValue);
        
        switch($normalizedStatus) {
            case 'ordered': return 'status-ordered';
            case 'preparing': return 'status-preparing';
            case 'ready_to_deliver': return 'status-ready';
            case 'delivery_on_the_way': return 'status-delivery';
            case 'delivered': return 'status-delivered';
            case 'completed': return 'status-completed';
            case 'cancelled': return 'status-cancelled';
            default: return 'status-ordered';
        }
    }

    // Helper method for status display
    public static function getStatusDisplay($status)
    {
        // Handle both string and OrderStatusEnum cases
        if ($status instanceof \App\Enums\OrderStatusEnum) {
            $statusValue = $status->value;
        } else {
            $statusValue = $status;
        }

        return ucwords(str_replace('_', ' ', $statusValue));
    }

    // Add this method to your OrderController
    public static function getOrderTimeline($orderStatus)
    {
        // Handle both string and OrderStatusEnum cases
        if ($orderStatus instanceof \App\Enums\OrderStatusEnum) {
            $currentStatus = $orderStatus->value;
        } else {
            $currentStatus = $orderStatus;
        }
        
        $currentStatus = strtolower($currentStatus);
        
        $statuses = [
            'ordered' => 'Order Placed',
            'preparing' => 'Preparing',
            'ready_to_deliver' => 'Ready',
            'delivery_on_the_way' => 'On the Way',
            'delivered' => 'Delivered',
            'completed' => 'Completed'
        ];
        
        return [
            'statuses' => $statuses,
            'currentStatus' => $currentStatus
        ];
    }

    public function getCustomerAddress($id): JsonResponse
    {
        try {
            $customer = Customer::find($id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'address' => ''
                ]);
            }

            return response()->json([
                'success' => true,
                'address' => $customer->address ?? ''
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'address' => ''
            ]);
        }
    }

    public function search(Request $request): View
    {
        $keyword = $request->input('search');

        $search = FoodMenu::where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
            ->orWhere('price', 'like', '%' . $keyword . '%')
            ->orWhereHas('foodCategory', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            });
        })->get();

        Log::info([$keyword, $search]);

        return view('public.menu', ['menu' => $search]);
    }

    public function createOrder(Request $request): JsonResponse
    {
        $cartItem = $request->input('cartData');
        $totalPrice = $request->input('totalAmount');
        $tableNumber = $request->input('table_number');
        $contact = $request->input('customer_contact');
        $address = $request->input('customer_address');
        $paymentType = $request->input('payment_type');

        // Validate payment type
        if (!in_array($paymentType, ['cash', 'card'])) {
            return response()->json([
                'validation-error-message' => 'Invalid payment method selected.',
            ], 422);
        }

        // Get deliveryType from the first item in cartData
        $deliveryType = $cartItem[0]['deliveryType'] ?? null;

        // Validate address for doorstep delivery
        if ($deliveryType === 'Doorstep Delivery' && empty(trim($address))) {
            return response()->json([
                'validation-error-message' => 'Delivery address is required for doorstep delivery.',
            ], 422);
        }

        // For dine-in, check table logic
        if ($deliveryType === 'Restaurant Dine-in') {
            $table = DiningTable::where('table_name', $tableNumber)->first();

            if (!$table) {
                return response()->json([
                    'validation-error-message' => 'Table does not exist. Please enter a correct table number.',
                ], 422);
            }

            if ($table->isOccupied) {
                return response()->json([
                    'validation-error-message' => 'Table is taken. Please enter another table number.',
                ], 422);
            }

            $table->update(['isOccupied' => true]);
            $tableId = $table->id;
        } else {
            $tableId = null;
        }

        // Update customer address if provided and customer exists
        $customerId = session('customer_id');
        if ($customerId && $address) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $customer->update([
                    'address' => $address
                ]);
                Log::info('Customer address updated for ID: ' . $customerId);
            }
        }

        // Create order with customer_id from session
        $order = CustomerOrder::create([
            'customer_id' => $customerId,
            'dining_table_id' => $tableId,
            'order_total_price' => $totalPrice,
            'delivery_type' => $deliveryType,
            'payment_type' => $paymentType,
            'isPaid' => false,
            'order_status' => OrderStatusEnum::Ordered,
            'customer_contact' => $contact,
            'delivery_address' => $deliveryType === 'Doorstep Delivery' ? $address : null,
        ]);

        foreach ($cartItem as $item) {
            CustomerOrderDetail::create([
                'order_id' => $order->id,
                'food_id' => $item['id'],
                'quantity' => $item['quantity'],
                'total_price' => $item['eachTotalPrice'],
            ]);
        }

        Log::info('Order created for customer ID: ' . $customerId . ', Order ID: ' . $order->id);

        return response()->json([
            'success-message' => 'Your order is being processed. Please wait 15–30 minutes.',
            'order_id' => $order->id
        ]);
    }

    public function makeReservation(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'book_name' => 'required|max:255',
            'book_email' => 'required|email',
            'book_phone' => 'required|numeric',
            'guest_number' => 'required|numeric|min:2',
            'book_date' => 'required|date|after:today',
            'book_time' => 'required|date_format:H:i',
            'book_message' => 'max:999999'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store reservation data with customer_id
        $reservation = Reservation::create([
            'customer_id' => session('customer_id'),
            'reservation_name' => $request->book_name,
            'reservation_email' => $request->book_email,
            'reservation_contact' => $request->book_phone,
            'reservation_attendees' => $request->guest_number,
            'reservation_date' => $request->book_date,
            'reservation_time' => $request->book_time,
            'reservation_message' => $request->book_message,
            'dining_table_id' => null,
            'reservation_status' => ReservationStatusEnum::Pending,
        ]);

        Log::info('Reservation created for customer ID: ' . session('customer_id') . ', Reservation ID: ' . $reservation->id);

        return back()->with('success-message', 'We have received your reservation. We will process immediately and we will contact you as soon as possible. Thank you.');
    }
}