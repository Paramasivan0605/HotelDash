<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'food_id', 'quantity', 'delivery_type', 'location_id'
    ];

    public function food()
    {
        return $this->belongsTo(FoodMenu::class, 'food_id'); // Adjust model name if needed
    }
}
