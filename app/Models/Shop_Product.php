<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_Product extends Model
{
    use HasFactory;
    protected $table = 'shop_products';
    protected $fillable = [
        'shop_id',
        'product_id',
        'price',
        'quantity',
        'warranty',
        'status'
    ];
    
    
}
