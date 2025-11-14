<?php

namespace App\Http\Controllers\Staff\Order;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\DiningTable;
use App\Models\RestaurantItem;
use App\Models\OrderHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrderControler extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (!$user || !$user->location_id) {
            return view('company.auth.login')->withErrors('User is not authenticated or location is not assigned.');
        }

        $locationId = $user->location_id;
        $staffId = $user->id;

        $orders = CustomerOrder::with(['customer', 'diningTable', 'customerOrderDetail.foodMenu', 'assignedStaff'])
            ->when($locationId, function($query) use ($locationId) {
                return $query->where('location_id', $locationId);
            })
            ->visibleToStaff($staffId)
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

        $myAssignedCount = CustomerOrder::where('location_id', $locationId)
            ->where('assigned_staff_id', $staffId)
            ->count();

        $availableCount = CustomerOrder::where('location_id', $locationId)
            ->whereNull('assigned_staff_id')
            ->count();

        $totalCount = CustomerOrder::where('location_id', $locationId)
            ->where('assigned_staff_id', $staffId)
            ->where('order_status', 'completed')
            ->count();


        return view('company.staff.order.index', [
            'customerOrder' => $orders,
            'currentLocationId' => $locationId,
            'currentStaffId' => $staffId,
            'myAssignedCount' => $myAssignedCount,
            'availableCount' => $availableCount,
            'totalCount' => $totalCount
        ]);
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
    *  Function to accept order (assign to current staff)
    */
    public function acceptOrder($id): RedirectResponse
    {
        $staffId = Auth::id();
        $order = CustomerOrder::findOrFail($id);

        // Check if order is already assigned to someone else
        if ($order->assigned_staff_id && $order->assigned_staff_id != $staffId) {
            return back()->withErrors(['error-message' => 'This order is already accepted by another staff member.']);
        }

        $order->update([
            'assigned_staff_id' => $staffId
        ]);

        // Log history
        OrderHistory::create([
            'order_id' => $order->id,
            'staff_id' => $staffId,
            'action' => 'accepted',
            'notes' => 'Order accepted by staff'
        ]);

        Log::info("Order {$order->id} accepted by staff {$staffId}");

        return back()->with('success-message', 'Order successfully accepted.');
    }

    /*
    *  Function to unaccept/cancel assignment
    */
    public function unacceptOrder($id): RedirectResponse
    {
        $staffId = Auth::id();
        $order = CustomerOrder::findOrFail($id);

        // Check if the order is assigned to current staff
        if ($order->assigned_staff_id != $staffId) {
            return back()->withErrors(['error-message' => 'You can only unaccept orders assigned to you.']);
        }

        $order->update([
            'assigned_staff_id' => null
        ]);

        // Log history
        OrderHistory::create([
            'order_id' => $order->id,
            'staff_id' => $staffId,
            'action' => 'unaccepted',
            'notes' => 'Order unaccepted by staff'
        ]);

        Log::info("Order {$order->id} unaccepted by staff {$staffId}");

        return back()->with('success-message', 'Order successfully unaccepted.');
    }

    /*
    *  Function to update order status resource
    */
    public function updateStatus(Request $request, $id): RedirectResponse
    {   
        $validated = $request->validate([
            'order_status' => 'required|in:Ordered,preparing,ready_to_deliver,delivery_on_the_way,delivered,completed,cancelled'
        ]);

        $staffId = Auth::id();
        $order = CustomerOrder::findOrFail($id);

        $oldStatus = $order->order_status;
        
        $order->update(['order_status' => $validated['order_status']]);

        // Log history
        OrderHistory::create([
            'order_id' => $order->id,
            'staff_id' => $staffId,
            'action' => 'status_changed',
            'old_status' => $oldStatus,
            'new_status' => $validated['order_status'],
            'notes' => 'Order status updated'
        ]);

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

    /*
    *  Function to get order history as JSON
    */
    public function getOrderHistoryJson($id)
    {
        $histories = OrderHistory::with('staff')
            ->where('order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($history) {
                return [
                    'action' => $history->action,
                    'old_status' => $history->old_status,
                    'new_status' => $history->new_status,
                    'staff_name' => $history->staff->name ?? 'System',
                    'created_at' => $history->created_at->format('j M Y, g:i A'),
                    'notes' => $history->notes
                ];
            });

        return response()->json(['histories' => $histories]);
    }
}