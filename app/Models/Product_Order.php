<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Order extends Model
{
    use HasFactory;
    protected $table = 'product_orders';
    protected $fillable = [
        'order_id',
        'shop_product_id',
        'quantity',
        'total'
    ];
}
