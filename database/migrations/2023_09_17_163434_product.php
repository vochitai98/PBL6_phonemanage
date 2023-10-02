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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('seoTitle');
            $table->decimal('price',19,2);
            $table->string('color');
            $table->string('image')->nullable();
            $table->string('listImage')->nullable();
            $table->string('fowardCameras');
            $table->string('backwardCameras');
            $table->boolean('isNew');
            $table->integer('quantity');
            $table->string('memoryStorage');
            $table->string('VAT');
            $table->string('warranty')->nullable();
            $table->boolean('status');
            $table->text('screem');
            $table->boolean('isTrending')->nullable();
            $table->text('description')->nullable();
            $table->text('details')->nullable();
            $table->integer('viewCount')->nullable();
            $table->integer('starRated')->nullable();
            $table->text('metaKeywords');
            $table->text('metaDescriptions');

            $table->unsignedBigInteger('id_brand');
            $table->foreign('id_brand')->references('id')->on('brands')->onDelete('cascade');

            $table->unsignedBigInteger('id_supplier');
            $table->foreign('id_supplier')->references('id')->on('suppliers')->onDelete('cascade');
            $table->timestamps();
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