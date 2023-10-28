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
        Schema::create('Product_Category',function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('seoTitle');
            $table->integer('parent_id');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('sort');
            $table->boolean('status');
            $table->string('metaKeywords');
            $table->string('metaDescriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_product_category');
    }
};
