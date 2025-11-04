<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case Ordered = 'Ordered';                 // New order placed
    case Preparing = 'preparing';             // Kitchen is preparing the food
    case ReadyToDeliver = 'ready_to_deliver'; // Food ready for delivery or serving
    case DeliveryOnTheWay = 'delivery_on_the_way'; // Delivery person picked up
    case Delivered = 'delivered';             // Order delivered to customer
    case Completed = 'completed';             // Order finalized / paid
    case Cancelled = 'cancelled';             // Order cancelled
}
