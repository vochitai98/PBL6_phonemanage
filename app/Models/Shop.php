<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'shopName',
        'shopAddress',
        'shopPhone',
        'state',
        'bankAccount',
    ];
}
