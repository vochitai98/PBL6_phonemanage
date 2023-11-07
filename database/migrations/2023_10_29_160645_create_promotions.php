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
            $table->unsignedBigInteger('shop_product_id');
            $table->integer('promotionPercentage')->nullable();
            $table->integer('promotionReduction')->nullable();
            $table->string('detail', 255);
            $table->boolean('status');
            $table->integer('quantity');
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->timestamps();
            $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
