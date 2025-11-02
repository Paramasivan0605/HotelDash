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
    public function login(): View
    {
        return view('public.login');
    }

    public function submit(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'mobile' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Find customer by mobile number (regardless of name)
        $customer = Customer::where('mobile', $validated['mobile'])->first();

        if ($customer) {
            Log::info('customer logged in: ' . $customer->name . ' (ID: ' . $customer->id . ')');
        } else {
            // Create new customer
            $customer = Customer::create([
                'name' => $validated['name'],
                'mobile' => $validated['mobile']
            ]);
            Log::info('New customer created: ' . $customer->name . ' (ID: ' . $customer->id . ')');
        }

        // Store customer ID in session
        session(['customer_id' => $customer->id]);
        session(['customer_name' => $customer->name]);
        session(['customer_mobile' => $customer->mobile]);

        Log::info('Login submitted for customer: ' . $customer->name . ' (ID: ' . $customer->id . ')');

        return redirect()->route('home')
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

        return view('public.locationMenu', compact('foodMenu', 'location'));
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
        $paymentType = $request->input('payment_type');

        // Validate payment type
        if (!in_array($paymentType, ['cash', 'card'])) {
            return response()->json([
                'validation-error-message' => 'Invalid payment method selected.',
            ], 422);
        }

        // Get deliveryType from the first item in cartData
        $deliveryType = $cartItem[0]['deliveryType'] ?? null;

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

        // Create order with customer_id from session
        $order = CustomerOrder::create([
            'customer_id' => session('customer_id'),
            'dining_table_id' => $tableId,
            'order_total_price' => $totalPrice,
            'delivery_type' => $deliveryType,
            'payment_type' => $paymentType,
            'isPaid' => false,
            'order_status' => OrderStatusEnum::Preparing,
            'customer_contact' => $contact,
        ]);

        foreach ($cartItem as $item) {
            CustomerOrderDetail::create([
                'order_id' => $order->id,
                'food_id' => $item['id'],
                'quantity' => $item['quantity'],
                'total_price' => $item['eachTotalPrice'],
            ]);
        }

        Log::info('Order created for customer ID: ' . session('customer_id') . ', Order ID: ' . $order->id);

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