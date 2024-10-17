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
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('business_id')->primary();  
            $table->string('business_image')->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('business_Name');
            $table->string('business_Email');
            $table->string('business_Province');
            $table->string('business_City');
            $table->string('business_Barangay');
            $table->string('business_Address');
            $table->string('business_Phone_Number');
            $table->string('business_Telephone_Number');
            $table->unsignedBigInteger('business_TIN');
            $table->string('business_Facebook')->nullable();
            $table->string('business_X')->nullable();
            $table->string('business_Instagram')->nullable();
            $table->string('business_Tiktok')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
