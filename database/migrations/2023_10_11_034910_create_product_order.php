<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); 
            $table->unsignedBigInteger('shop_product_id'); 
            $table->integer('quantity');
            $table->timestamps();
        
            // Khóa ngoại đến bảng đơn hàng và bảng sản phẩm
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('cascade');
            //set 2 khoa ngoai ko cùng cặp xãy ra
            $table->unique(['shop_product_id', 'order_id']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_order');
    }
};
