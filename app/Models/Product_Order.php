<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'product_id',
        'price',
    ];
}
