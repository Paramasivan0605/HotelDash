<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'mobile',
    ];

    public function orders()
    {
        return $this->hasMany(CustomerOrder::class, 'customer_id');
    }
}
