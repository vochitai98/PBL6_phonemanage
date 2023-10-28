<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shops';
    protected $fillable = [
        'customer_id',
        'shopName',
        'shopAddress',
        'shopPhone',
        'state',
        'bankAccount',
        'vnp_TmnCode',
        'vnp_HashSecret',
    ];
}
