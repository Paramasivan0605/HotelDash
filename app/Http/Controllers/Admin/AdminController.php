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
            ->where('location.currency', 'THB')->where('isPaid',1);
        $lkrQuery = CustomerOrder::join('location', 'customer_orders.location_id', '=', 'location.location_id')
            ->where('location.currency', 'LKR')->where('isPaid',1);

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
        $selectedStatus = $request->input('status');
        $selectedPaymentStatus = $request->input('payment_status');
        $selectedDeliveryType = $request->input('delivery_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $searchQuery = $request->input('search');

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

        if ($selectedStatus) {
            $ordersQuery->where('order_status', $selectedStatus);
        }

        if ($selectedPaymentStatus !== null && $selectedPaymentStatus !== '') {
            $ordersQuery->where('isPaid', $selectedPaymentStatus);
        }

        if ($selectedDeliveryType) {
            $ordersQuery->where('delivery_type', $selectedDeliveryType);
        }

        if ($dateFrom) {
            $ordersQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $ordersQuery->whereDate('created_at', '<=', $dateTo);
        }

        if ($searchQuery) {
            $ordersQuery->where(function($query) use ($searchQuery) {
                $query->where('id', 'like', '%' . $searchQuery . '%')
                    ->orWhere('customer_contact', 'like', '%' . $searchQuery . '%')
                    ->orWhereHas('customer', function($q) use ($searchQuery) {
                        $q->where('name', 'like', '%' . $searchQuery . '%');
                    });
            });
        }

        // Get orders with pagination
        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        // Get staff and locations for filter dropdowns
        $staffList = User::where('role', 2)
            ->orderBy('name')
            ->get();

        $locationList = Location::orderBy('location_name')->get();

        // Get order statuses from enum
        $statusList = OrderStatusEnum::cases();

        // Delivery types
        $deliveryTypes = ['Pickup', 'Delivery'];

        return view('company.admin.delivery-management', [
            'orders'                => $orders,
            'staffList'             => $staffList,
            'locationList'          => $locationList,
            'statusList'            => $statusList,
            'deliveryTypes'         => $deliveryTypes,
            'selectedStaff'         => $selectedStaff,
            'selectedLocation'      => $selectedLocation,
            'selectedStatus'        => $selectedStatus,
            'selectedPaymentStatus' => $selectedPaymentStatus,
            'selectedDeliveryType'  => $selectedDeliveryType,
            'dateFrom'              => $dateFrom,
            'dateTo'                => $dateTo,
            'searchQuery'           => $searchQuery,
        ]);
    }
}