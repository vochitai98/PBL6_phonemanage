<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
                'name','seoTitle','color','image','forwardCameras','backwardCameras', 'memoryStorage','VAT', 'status','screen','isTrending','brand_id','metaKeywords','metaDescriptions','type','sim','battery'
    ];
                protected $guarded = ['id', 'created_at', 'updated_at'];
}
