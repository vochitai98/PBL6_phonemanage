<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
                'name','seoTitle','color','image','listImage','forwardCameras','backwardCameras','isNew', 'memoryStorage','VAT', 'status','screen','isTrending','starRated','viewCount','brand_id','metaKeywords','metaDescriptions','type','sim','battery'
    ];
                protected $guarded = ['id', 'created_at', 'updated_at'];
}
