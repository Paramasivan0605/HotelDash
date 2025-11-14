<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerOrder extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'customer_id',
        'dining_table_id',
        'delivery_type',
        'order_total_price',
        'payment_type',
        'isPaid',
        'order_status',
        'customer_contact',
        'location_id',
        'assigned_staff_id' // Add this
    ];

    protected $casts = [
        'order_status' => OrderStatusEnum::class,
    ];

    public function customerOrderDetail() : HasMany
    {
        return $this->hasMany(CustomerOrderDetail::class, 'order_id');
    }

    public function diningTable() : BelongsTo
    {
        return $this->belongsTo(DiningTable::class, 'dining_table_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function location() : BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    // New relationships
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_id');
    }

    // Scopes
    public function scopeVisibleToStaff($query, $staffId)
    {
        return $query->where(function($q) use ($staffId) {
            $q->where('assigned_staff_id', $staffId)
              ->orWhereNull('assigned_staff_id');
        });
    }

    public function scopeAssignedToMe($query, $staffId)
    {
        return $query->where('assigned_staff_id', $staffId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_staff_id');
    }
}