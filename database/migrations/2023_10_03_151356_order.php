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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('orderDate')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('delivered')->nullable();
            $table->dateTime('deliveredDate')->nullable();
            $table->string('discount')->nullable();
            $table->dateTime('updatedDate')->nullable();
            $table->string('updatedBy')->nullable();
            $table->dateTime('deletedDate')->nullable();
            $table->string('deletedBy')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
