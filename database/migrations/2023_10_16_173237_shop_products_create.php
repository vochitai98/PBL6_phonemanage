<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
     public function up()
     {
         Schema::create('shop_products', function (Blueprint $table) {
             $table->id();
             $table->unsignedBigInteger('shop_id');
             $table->unsignedBigInteger('product_id');
             $table->decimal('price', 10, 2); // Sử dụng kiểu dữ liệu phù hợp với giá
             $table->integer('quantity');
             $table->boolean('status');
             $table->integer('warranty');
             $table->timestamps(); // Tự động quản lý created_at và updated_at
 
             $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
             $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
             $table->unique(['shop_id', 'product_id']);
         });
     }
 
     public function down()
     {
         Schema::dropIfExists('shop_products');
     }
 
};
