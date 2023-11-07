<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $table = 'promotions';
    protected $fillable = ['name','shop_product_id','promotionPercentage','promotionReduction','detail','status','quantity','startDate','endDate'];
}
