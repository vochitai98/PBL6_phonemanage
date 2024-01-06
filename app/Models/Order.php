<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'shop_id',
        'order_at',
        'status',
        'delivered',
        'delivered_at',
        'discount',
        'discount_amount',
        'paid',
        'total_price'
    ];
}
