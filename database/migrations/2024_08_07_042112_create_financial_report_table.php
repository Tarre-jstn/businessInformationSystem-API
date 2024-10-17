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
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->date('date'); 
            $table->string('category'); // String column for category
            $table->decimal('amount', 8, 2);
            $table->timestamps();
        
        });
        
        Schema::create('finance_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category')->unique(); // Ensure 'category' is unique
            $table->timestamps();
        });

        DB::table('finance_categories')->insert([
            ['category' => 'expense', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'income', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance');
        Schema::dropIfExists('finance_categories');
    }
};
