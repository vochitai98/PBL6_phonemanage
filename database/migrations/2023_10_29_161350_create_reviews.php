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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
             $table->unsignedBigInteger('shop_product_id');
             $table->string('feedback');
             $table->integer('rating');
             $table->timestamps(); // Tự động quản lý created_at và updated_at
 
             $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
             $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('cascade');
             $table->unique(['customer_id', 'shop_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
