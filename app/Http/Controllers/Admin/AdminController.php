<?php
namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Location;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index(Request $request): View
    {
        // Get selected location from request
        $selectedLocation = $request->input('location_id');

        // Base queries
        $staffQuery = User::where('role', 2);
        $salesQuery = CustomerOrder::query();
        $thbQuery = CustomerOrder::join('location', 'customer_orders.location_id', '=', 'location.location_id')
            ->where('location.currency', 'THB');
        $lkrQuery = CustomerOrder::join('location', 'customer_orders.location_id', '=', 'location.location_id')
            ->where('location.currency', 'LKR');

        // Apply location filter if selected
        if ($selectedLocation) {
            // Filter staff by location
            $staffQuery->where('location_id', $selectedLocation);
            
            // Filter orders by location
            $salesQuery->where('location_id', $selectedLocation);
            $thbQuery->where('customer_orders.location_id', $selectedLocation);
            $lkrQuery->where('customer_orders.location_id', $selectedLocation);
        }

        $staffcount = $staffQuery->count();
        $salescount = $salesQuery->count();
        $totalThbAmount = $thbQuery->sum('customer_orders.order_total_price');
        $totalLkrAmount = $lkrQuery->sum('customer_orders.order_total_price');

        // Get recent orders for the selected location
        $recentOrdersQuery = CustomerOrder::with(['customer', 'location', 'assignedStaff'])
            ->orderBy('created_at', 'desc');

        if ($selectedLocation) {
            $recentOrdersQuery->where('location_id', $selectedLocation);
        }

        $recentOrders = $recentOrdersQuery->limit(10)->get();

        // Get locations for filter dropdown
        $locationList = Location::orderBy('location_name')->get();

        return view('company.admin.dashboard', [
            'staffcount'       => $staffcount,
            'salescount'       => $salescount,
            'totalThbAmount'   => $totalThbAmount,
            'totalLkrAmount'   => $totalLkrAmount,
            'recentOrders'     => $recentOrders,
            'locationList'     => $locationList,
            'selectedLocation' => $selectedLocation,
        ]);
    }
    
    public function DeliveryManagement(Request $request): View
    {
        // Get filter values
        $selectedStaff = $request->input('staff_id');
        $selectedLocation = $request->input('location_id');

        // Build query with relationships
        $ordersQuery = CustomerOrder::with([
            'customer',
            'assignedStaff',
            'location',
            'histories' => function($query) {
                $query->with('staff')->orderBy('created_at', 'desc');
            }
        ]);

        // Apply filters
        if ($selectedStaff) {
            $ordersQuery->where('assigned_staff_id', $selectedStaff);
        }

        if ($selectedLocation) {
            $ordersQuery->where('location_id', $selectedLocation);
        }

        // Get orders with pagination
        $orders = $ordersQuery->orderBy('created_at', 'desc')->simplePaginate(10);
        // Get staff and locations for filter dropdowns
        $staffList = User::where('role', 2)
            ->orderBy('name')
            ->get();

        $locationList = Location::orderBy('location_name')->get();

        return view('company.admin.delivery-management', [
            'orders'           => $orders,
            'staffList'        => $staffList,
            'locationList'     => $locationList,
            'selectedStaff'    => $selectedStaff,
            'selectedLocation' => $selectedLocation,
        ]);
    }
}