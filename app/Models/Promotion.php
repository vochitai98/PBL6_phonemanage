<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $table = 'promotions';
    protected $fillable = ['code','shop_product_id','shop_id','type','value','minPriceCondition','detail','status','quantity','startDate','endDate'];
}
