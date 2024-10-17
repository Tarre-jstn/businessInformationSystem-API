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
        Schema::create('chatbot_response', function (Blueprint $table) {
            $table->bigIncrements('chatbot_id')->primary();  
            $table->string('chabot_BWHours')->nullable();
            $table->string('chabot_BPDescription')->nullable();
            $table->string('chabot_Lazada')->nullable();
            $table->string('chabot_Shopee')->nullable();
            $table->string('chabot_Region1')->nullable();
            $table->string('chabot_Region2')->nullable();
            $table->string('chabot_Region3')->nullable();
            $table->string('chabot_Region4A')->nullable();
            $table->string('chabot_Region4B')->nullable();
            $table->string('chabot_Region5')->nullable();
            $table->string('chabot_NCR')->nullable();
            $table->string('chabot_CAR')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_response');
    }
};
