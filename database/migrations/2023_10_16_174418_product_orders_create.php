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
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('shop_product_id');
            $table->integer('quantity');
            $table->timestamps();

        // Định nghĩa ràng buộc khóa ngoại
        $table->foreign('order_id')
            ->references('id')
            ->on('orders')
            ->onDelete('cascade');
        $table->foreign('shop_product_id')
            ->references('id')
            ->on('shop_products')
            ->onDelete('cascade');
        });


    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
