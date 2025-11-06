<?php

namespace App\Http\Controllers\Staff\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\DiningTable;
use App\Models\RestaurantItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OrderControler extends Controller
{
    public function index() : View
    {
        $order = CustomerOrder::with(['customer','diningTable', 'customerOrderDetail.foodMenu'])
        ->orderByRaw("
            CASE 
                WHEN order_status = 'Ordered' THEN 1
                WHEN order_status = 'preparing' THEN 2
                WHEN order_status = 'ready_to_deliver' THEN 3
                WHEN order_status = 'delivery_on_the_way' THEN 4
                WHEN order_status = 'delivered' THEN 5
                WHEN order_status = 'completed' THEN 6
                WHEN order_status = 'cancelled' THEN 7
                ELSE 8
            END
        ")
        ->orderBy('created_at', 'asc')
        ->get();

        return view('company.staff.order.index', ['customerOrder' => $order]);
    }

    /*
    *  Funtion to view create file
    */
    public function create() : View
    {
        $table = DiningTable::paginate(10);

        return view('company.staff.order.create', ['diningTable' => $table]);
    }

    /*
    *  Function to store data of dining table number
    */
    public function store(Request $request) : RedirectResponse
    {
        $validator = Validator::make($request->only('table_number'), [
            'table_number' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Retrieve a submitted input of table_number
        $validated = $validator->safe()->only('table_number');

        $tableName = 'Dining Table';

        // Get dining table
        $diningTable = RestaurantItem::where('item_name', 'like', $tableName)->first();

        // Get quantity
        $tableQuantity = $diningTable->quantity;

        // Get number of table no. registered
        $tableRegistered = DiningTable::count();

        // Check if all dining table is registered or not
        if ($tableRegistered > $tableQuantity) {
            return back()->withErrors([
                'validation-error-message' => 'All table registered already. Cannot registered more than table have.'
            ])->withInput();
        }

        // Check if table_name already exists
        $exists = DiningTable::where('table_name', $validated)->first();

        if ($exists) {
            return back()->withErrors([
                'validation-error-message' => 'Table number is already registered. Please enter other number.'
            ])->withInput();
        }

        $created = DiningTable::create([
            'table_name' => $validated['table_number'],
            'isOccupied' => false,
        ]);

        Log::info($created);

        return back()->with('success-message', 'Table number successfully registered.');
    }

    /*
    *  Function to update order status resource
    */
    public function updateStatus(Request $request, $id): RedirectResponse
    {   
        $validated = $request->validate([
            'order_status' => 'required|in:Ordered,preparing,ready_to_deliver,delivery_on_the_way,delivered,completed,cancelled'
        ]);

        $order = CustomerOrder::findOrFail($id);
        $order->update(['order_status' => $validated['order_status']]);

        return back()->with('success-message', 'Order status updated successfully to ' . ucwords(str_replace('_', ' ', $validated['order_status'])) . '.');
    }

    public static function getStatusClass($status)
    {
        // Handle both string and OrderStatusEnum cases
        if ($status instanceof OrderStatusEnum) {
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

    // Helper method to get display text for status
    public static function getStatusDisplay($status)
    {
        // Handle both string and OrderStatusEnum cases
        if ($status instanceof OrderStatusEnum) {
            $statusValue = $status->value;
        } else {
            $statusValue = $status;
        }

        return ucwords(str_replace('_', ' ', $statusValue));
    }
    /*
    *  Function to update payment status resource
    */
    public function updatePaymentStatus(Request $request, $id): RedirectResponse
    {   
        $validated = $request->validate([
            'is_paid' => 'required|in:0,1'
        ]);

        $order = CustomerOrder::findOrFail($id);
        $order->update(['isPaid' => $validated['is_paid']]);

        $statusText = $validated['is_paid'] == '1' ? 'Paid' : 'Not Paid';

        return back()->with('success-message', 'Payment status updated successfully to ' . $statusText . '.');
    }
}