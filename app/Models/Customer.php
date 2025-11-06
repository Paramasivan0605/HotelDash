<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ correct base class
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable // ✅ extend Authenticatable, not Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'address',
    ];

    public function orders()
    {
        return $this->hasMany(CustomerOrder::class, 'customer_id');
    }
}
