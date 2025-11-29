<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\FoodCategory;
use App\Models\FoodMenu;
use App\Models\FoodLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderDetail;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Log; 

class LocationController extends Controller
{
    public function showOrderPage()
    {
        // Get all locations from database
        $locations = Location::get();
        
        return view('public.login', [
            'locations' => $locations
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:delivery,pickup',
            'location_id' => 'required|exists:location,location_id'
        ]);

        // Store order type and location in session
        session([
            'order_type' => $request->input('order_type'),
            'location_id' => $request->input('location_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully',
        ]);
    }

    public function home()
    {
        // Get location ID from session
        $locationId = session('location_id');
        
        if (!$locationId) {
            return redirect()->route('public.login')->with('error', 'Please select a location first');
        }

        // Get location details
        $location = Location::findOrFail($locationId);

        // Method 3: Using query builder with joins
        $categories = FoodCategory::select('food_categories.*')
            ->join('food_menus', 'food_menus.category_id', '=', 'food_categories.id')
            ->join('food_price', 'food_price.food_id', '=', 'food_menus.id')
            ->where('food_price.location_id', $locationId)
            ->distinct()
            ->get();

        // Get food menu with price for this location, grouped by category
        $foodMenuByCategory = [];
        
        foreach ($categories as $category) {
            $foodItems = FoodMenu::select('food_menus.*', 'food_price.price')
                ->join('food_price', 'food_price.food_id', '=', 'food_menus.id')
                ->where('food_menus.category_id', $category->id)
                ->where('food_price.location_id', $locationId)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'image' => $item->image,
                        'price' => $item->price,
                        'category_id' => $item->category_id,
                    ];
                });

            $foodMenuByCategory[$category->id] = [
                'category' => $category,
                'items' => $foodItems
            ];
        }

        return view('public.home', [
            'location' => $location,
            'categories' => $categories,
            'foodMenuByCategory' => $foodMenuByCategory,
            'orderType' => session('order_type', 'delivery')
        ]);
    }
    
 public function checkout(Request $request): JsonResponse
{
    try {
        Log::info('Order request received', $request->all());

        // Validate request
        $validated = $request->validate([
            'customer_contact' => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'payment_type' => 'required|in:cash,card',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|exists:food_menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'order_notes' => 'nullable|string|max:500'
        ]);

        $locationId = session('location_id');
        
        if (!$locationId) {
            return response()->json([
                'validation-error-message' => 'Location not found. Please select a location first.',
            ], 422);
        }

        // Get location details
        $location = Location::find($locationId);
        if (!$location) {
            return response()->json([
                'validation-error-message' => 'Invalid location selected.',
            ], 422);
        }
          // Set timezone based on location
            $timezones = [
                1 => 'Asia/Bangkok',   // Phuket
                2 => 'Asia/Bangkok',   // Bangkok
                3 => 'Asia/Bangkok',   // Pattaya
                4 => 'Asia/Colombo',   // Colombo
            ];
            date_default_timezone_set($timezones[$locationId] ?? 'Asia/Bangkok');

        DB::beginTransaction();

        // Create or find customer
        $customer = Customer::firstOrCreate(
            ['mobile' => $validated['customer_contact']],
            [
                'name' => 'Guest Customer',
                'address' => $validated['customer_address'],
            ]
        );

        // Update address if it changed
        if ($customer->address !== $validated['customer_address']) {
            $customer->update(['address' => $validated['customer_address']]);
        }

        // Generate custom order ID based on location
        $orderId = $this->generateLocationBasedOrderId($location->location_name);
        $orderCode = $orderId; // Using the same ID as order code

        // Create order with custom ID
        $order = CustomerOrder::create([
            'id' => $orderId,
            'order_code' => $orderCode,
            'customer_id' => $customer->id,
            'location_id' => $locationId,
            'dining_table_id' => null,
            'order_total_price' => $validated['total_amount'],
            'delivery_type' => session('order_type', 'delivery'),
            'payment_type' => $validated['payment_type'],
            'isPaid' => $validated['payment_type'] === 'card',
            'order_status' => OrderStatusEnum::Ordered,
            'customer_contact' => $validated['customer_contact'],
            'customer_address' => $validated['customer_address'],
            'order_notes' => $validated['order_notes'] ?? null,
        ]);

        // Create order details
        foreach ($validated['items'] as $item) {
            CustomerOrderDetail::create([
                'order_id' => $order->id,
                'food_id' => $item['food_id'],
                'quantity' => $item['quantity'],
                'total_price' => $item['price'] * $item['quantity'],
                'unit_price' => $item['price'],
            ]);
        }

        DB::commit();

        Log::info('Order created successfully', [
            'order_id' => $order->id,
            'order_code' => $orderCode,
            'customer_id' => $customer->id,
            'location_id' => $locationId,
            'location_name' => $location->location_name
        ]);

        return response()->json([
            'success-message' => 'Your order has been placed successfully! Order Code: ' . $orderCode,
            'order_id' => $order->id,
            'order_code' => $orderCode
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Validation error in order creation', [
            'errors' => $e->errors()
        ]);
        return response()->json([
            'validation-error-message' => implode(', ', $e->validator->errors()->all()),
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order creation failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'validation-error-message' => 'An error occurred while processing your order: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Generate location-based order ID
 */
private function generateLocationBasedOrderId(string $locationName): string
{
    // Define location prefixes
    $locationPrefixes = [
        'thailand' => 'thai',
        'bangkok' => 'BAN',
        'colombo' => 'col',
        'phuket' => 'phu',
        'pattaya' => 'pat',
    ];

    // Get the prefix (default to first 3 letters if not in array)
    $prefix = $locationPrefixes[strtolower($locationName)] ?? strtoupper(substr($locationName, 0, 3));

    // Generate random 8-digit number
    $randomNumber = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

    // Combine prefix and random number
    $orderId = $prefix . $randomNumber;

    // Check if this ID already exists (very unlikely but just in case)
    $attempts = 0;
    while (CustomerOrder::where('id', $orderId)->exists() && $attempts < 5) {
        $randomNumber = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $orderId = $prefix . $randomNumber;
        $attempts++;
    }

    return $orderId;
}
}