<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $table = 'promotions';
    protected $fillable = ['name','product_id','shop_id','promotionPercentage','promotionReduction','detail','status','quantity','startDate','endDate'];
}
