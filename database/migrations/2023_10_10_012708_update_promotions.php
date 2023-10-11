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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shop_id');
            $table->integer('promotionPercentage')->nullable();
            $table->integer('promotionReduction')->nullable();
            $table->string('detail', 255);
            $table->boolean('status');
            $table->integer('quantity');
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->timestamps();

            // Thêm ràng buộc duy nhất cho cặp product_id và shop_id
            $table->unique(['product_id', 'shop_id']);

            // Đặt ràng buộc khóa ngoại cho product_id và shop_id
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
