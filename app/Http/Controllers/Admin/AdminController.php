<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index() : View
    {
        $staff = User::where('role', 2)->get();

        $sales = CustomerOrder::where('order_status', OrderStatusEnum::Completed)->sum('order_total_price');

        return view('company.admin.dashboard', ['staff' => $staff, 'sales' => $sales]);
    }
    
    public function DeliveryManagement(): View
    {
        // Get all orders with relationships, ordered by most recent first
        $orders = CustomerOrder::with([
            'customer',
            'assignedStaff',
            'location',
            'histories' => function($query) {
                $query->with('staff')->orderBy('created_at', 'desc');
            }
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('company.admin.delivery-management', compact('orders'));
    }
}