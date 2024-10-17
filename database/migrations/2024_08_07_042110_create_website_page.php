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
        Schema::create('websites', function (Blueprint $table) {
            $table->increments('website_ID');
            // $table->unsignedBigInteger('business_id');
            // $table->unsignedBigInteger('product_id');
            // $table->foreign('business_id')->references('business_id')->on('business')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('business_id')->references('business_id')->on('businesses')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->references('id')->on('products')->onDelete('cascade');
            $table->string('website_description')->nullable();
            $table->string('website_details')->nullable();
            $table->string('website_image')->nullable();
            $table->string('about_us1')->nullable();
            $table->string('about_us2')->nullable();
            $table->string('about_us3')->nullable();
            $table->string('website_footNote')->nullable();
            $table->enum('featured_section',['true','false'])->default('true');
            $table->enum('onSale_section',['true','false'])->default('true');
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_page');
    }
};
